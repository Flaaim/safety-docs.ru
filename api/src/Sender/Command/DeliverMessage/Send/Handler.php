<?php

namespace App\Sender\Command\DeliverMessage\Send;

use App\Sender\Entity\Recipient;
use App\Sender\Service\Message\CreatorInterface;
use Symfony\Component\Mailer\MailerInterface;

class Handler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly CreatorInterface $creator,
    ){
    }

    public function handle(Recipient $recipient): void
    {
        $message = $this->creator->create($recipient);
        $this->mailer->send($message);
    }
}