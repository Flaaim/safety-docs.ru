<?php

namespace App\Sender\Entity;

use App\Shared\Domain\File\AttachableFileInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Recipient
{
    /** @var array<AttachableFileInterface> $attachments */
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

    public function addAttachment(AttachableFileInterface $file): void
    {
        if(!$file->exists()){
            throw new \DomainException("File '{$file->getValue()}' does not exists.");
        }
        $this->attachments[] = $file;
    }
    /** @return array<int, AttachableFileInterface> */
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