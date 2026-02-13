<?php

namespace App\Sender\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "messages")]
class Message
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'message_id', unique: true)]
        private MessageId $id,
        #[ORM\Embedded(class: Recipient::class)]
        private Recipient $recipient,
        #[ORM\Column(type: 'message_status')]
        private MessageStatus $status,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $dateReceived,
    ){
    }

    public function getId(): MessageId
    {
        return $this->id;
    }
    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }
    public function getStatus(): MessageStatus
    {
        return $this->status;
    }
    public function getDateReceived(): \DateTimeImmutable
    {
        return $this->dateReceived;
    }
    private function setStatus(MessageStatus $status): void
    {
        $this->status = $status;
    }
    public function updateStatus(MessageStatus $status): void
    {
        $this->setStatus($status);
    }
}