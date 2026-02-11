<?php

namespace App\Sender\Command\Send;

use App\Product\Entity\File;
use App\Sender\Entity\Recipient;
use App\Sender\Service\Message\CreatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class Handler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
        private readonly CreatorInterface $creator,
    )
    {

    }
    public function handle(Recipient $recipient): void
    {
        try{
            $message = $this->creator->create($recipient);
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Failed to send mail: ', [
                'error' => $e->getMessage(),
                'recipient' => $recipient->getEmail()->getValue(),
            ]);
            throw $e;
        }


    }
}