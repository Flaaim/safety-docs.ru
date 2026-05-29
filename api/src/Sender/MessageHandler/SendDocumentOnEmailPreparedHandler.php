<?php

declare(strict_types=1);

namespace App\Sender\MessageHandler;

use App\Flusher;
use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageId;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\MessageStatus;
use App\Sender\Entity\Recipient;
use App\Sender\Event\SendDocumentEmailCommand;
use App\Sender\Service\Message\CreatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendDocumentOnEmailPreparedHandler
{
    public function __construct(
        private readonly MessageRepository $messages,
        private readonly Flusher $flusher,
        private readonly LoggerInterface $logger,
        private readonly CreatorInterface $creator,
        private readonly MailerInterface $mailer
    ) {
    }
    public function __invoke(SendDocumentEmailCommand $event): void
    {
        $recipient = new Recipient(
            new EmailMessage($event->email),
            $event->subject,
        );

        $recipient->addAttachment($event->pathToFile);

        $message = new Message(
            MessageId::generate(),
            $recipient,
            MessageStatus::pending(),
            new \DateTimeImmutable()
        );

        $this->messages->add($message);
        $this->flusher->flush();

        try {
            $mimeMessage = $this->creator->create($message->getRecipient());

            $this->mailer->send($mimeMessage);

            $message->updateStatus(MessageStatus::received());
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send message', [
                'message_id' => $message->getId()->getValue(),
                'recipient' => $message->getRecipient()->getEmail()->getValue(),
            ]);
            $message->updateStatus(MessageStatus::failed());
            $this->flusher->flush();

            throw $e;
        }
    }
}
