<?php

namespace App\Sender\Command\Send;

use App\Flusher;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageId;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\MessageStatus;
use App\Sender\Service\Message\CreatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;

class Handler
{

    public function __construct(
        private readonly MessageRepository $messages,
        private readonly Flusher $flusher,
        private readonly LoggerInterface $logger,
        private readonly CreatorInterface $creator,
        private readonly MailerInterface $mailer
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

        $this->messages->add($message);

        try{

            $mimeMessage = $this->creator->create($message->getRecipient());

            $this->mailer->send($mimeMessage);

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