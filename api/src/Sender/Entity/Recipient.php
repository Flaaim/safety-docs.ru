<?php

namespace App\Sender\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Recipient
{
    /** @var array<string[]> $attachments */
    #[ORM\Column(type: 'json')]
    private array $attachments;
    public function __construct(
        #[ORM\Column(type: 'email_message')]
        private readonly EmailMessage $email,
        #[ORM\Column(type: 'string')]
        private readonly string $subject
    ){
        $this->attachments = [];
    }

    public function addAttachment(string $absolutePathToFile): void
    {
        $this->attachments[] = $absolutePathToFile;
    }
    /** @return array<int, string> */
    public function getAttachments(): array
    {
        return $this->attachments;
    }
    public function getSubject(): string
    {
        return $this->subject;
    }
    public function getEmail(): EmailMessage
    {
        return $this->email;
    }
}