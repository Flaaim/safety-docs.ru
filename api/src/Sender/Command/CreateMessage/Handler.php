<?php

namespace App\Sender\Command\CreateMessage;

use App\Flusher;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageId;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\MessageStatus;

class Handler
{

    public function __construct(
        private readonly MessageRepository $messages,
        private readonly Flusher $flusher
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
    }
}