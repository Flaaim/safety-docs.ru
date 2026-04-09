<?php

use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageId;
use App\Sender\Entity\MessageStatus;
use App\Sender\Entity\Recipient;

class MessageBuilder
{
    private string $messageId;
    private Recipient $recipient;
    private MessageStatus $status;
    private DateTimeImmutable $dateReceived;

    public function __construct()
    {
        $this->messageId = new MessageId('14334de0-229d-4ca3-b6f3-7ba366688540');
        $this->recipient = new Recipient(new EmailMessage('test@email.ru'), 'subject');
        $this->status = MessageStatus::pending();
        $this->dateReceived = new \DateTimeImmutable('now');
    }
    public function withId(MessageId $messageId): self
    {
        $this->messageId = $messageId;
        return $this;
    }
    public function withRecipient(Recipient $recipient): self
    {
        $this->recipient = $recipient;
        return $this;
    }
    public function withStatus(MessageStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function build()
    {
        return new Message(
            $this->messageId,
            $this->recipient,
            $this->status,
            $this->dateReceived,
        );
    }
}