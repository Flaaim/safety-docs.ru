<?php

namespace App\Sender\Command\Send;

use App\Product\Entity\File;
use App\Sender\Entity\Recipient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class Handler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly LoggerInterface $logger
    )
    {
    }
    public function handle(Recipient $recipient): void
    {
        $message = new \Symfony\Component\Mime\Email();
        $message->subject($recipient->getSubject());
        $message->to($recipient->getEmail()->getValue());
        $message->html(
            $this->twig->render('mail/template.html.twig')
        );
        /** @var File $attachment */
        foreach ($recipient->getAttachments() as $attachment) {
            $message->attachFromPath($attachment->getFullPath());
        }

        try{
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Failed to send mail: ', ['error' => $e->getMessage()]);
            throw new TransportException($e->getMessage());
        }


    }
}