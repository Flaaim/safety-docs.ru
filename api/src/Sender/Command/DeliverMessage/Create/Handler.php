<?php

namespace App\Sender\Command\DeliverMessage\Create;

use App\Flusher;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageId;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\MessageStatus;
use App\Sender\Command\DeliverMessage\Send\Handler as SendHandler;
use Psr\Log\LoggerInterface;

class Handler
{

    public function __construct(
        private readonly MessageRepository $messages,
        private readonly Flusher $flusher,
        private readonly SendHandler $sendHandler,
        private readonly LoggerInterface $logger
    ){
    }

    public function handle(Command $command): void
    {
        $message = new Message(
            MessageId::generate(),
            $command->recipient,
            MessageStatus::pending(),
            new \DateTimeImmutable()
        );

        $this->messages->create($message);

        try{

            $this->sendHandler->handle($message->getRecipient());

            $message->updateStatus(MessageStatus::received());

        }catch (\Exception $e){
            $this->logger->error('Failed to send message', [
                'message_id' => $message->getId()->getValue(),
                'recipient' => $message->getRecipient()->getEmail()->getValue(),
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);
            $message->updateStatus(MessageStatus::failed());

        }

        $this->messages->update($message);

        $this->flusher->flush();

    }
}