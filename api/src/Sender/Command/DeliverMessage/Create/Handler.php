<?php

namespace App\Sender\Command\DeliverMessage\Create;

use App\Flusher;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageId;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\MessageStatus;
use App\Shared\Domain\Event\Message\CreateMessageEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Handler
{

    public function __construct(
        private readonly MessageRepository $messages,
        private readonly Flusher $flusher,
        private readonly EventDispatcher $dispatcher
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

        $this->flusher->flush();

        $event = new CreateMessageEvent($message);
        $this->dispatcher->dispatch($event);
    }
}