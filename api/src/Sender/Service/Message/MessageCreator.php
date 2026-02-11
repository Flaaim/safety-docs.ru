<?php

namespace App\Sender\Service\Message;

use App\Sender\Entity\Recipient;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MessageCreator implements CreatorInterface
{
    public function __construct(
        private readonly Environment $twig,
        private readonly string $template = 'mail/template.html.twig'
    ){
    }
    public function create(Recipient $recipient): Email
    {
        $message = (new Email())
            ->subject($recipient->getSubject())
            ->to($recipient->getEmail()->getValue())
            ->html($this->twig->render($this->template));

        foreach ($recipient->getAttachments() as $attachment) {
            $message->attachFromPath($attachment->getFile());
        }

        return $message;
    }
}