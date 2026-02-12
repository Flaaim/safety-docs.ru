<?php

namespace App\Sender\Entity;

use App\Shared\Domain\File\AttachableFileInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Recipient
{
    /** @var ArrayCollection<AttachableFileInterface> $attachments */
    private Collection $attachments;
    public function __construct(
        private readonly EmailMessage $email,
        private readonly string $subject
    ){
        $this->attachments = new ArrayCollection();
    }

    public function addAttachment(AttachableFileInterface $file): void
    {
        if(!$file->exists()){
            throw new \DomainException("File '{$file->getValue()}' does not exists.");
        }
        $this->attachments->add($file);
    }
    /** @return Collection<int, AttachableFileInterface> */
    public function getAttachments(): Collection
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