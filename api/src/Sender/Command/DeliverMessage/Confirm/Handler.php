<?php

namespace App\Sender\Command\DeliverMessage\Confirm;

use App\Flusher;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\MessageStatus;


class Handler
{

    public function __construct(
        private readonly MessageRepository $messages,
        private readonly Flusher $flusher
    ){

    }
    public function handle(Message $message): void
    {
        $message->updateStatus(MessageStatus::received());

        $this->messages->update($message);

        $this->flusher->flush();
    }
}