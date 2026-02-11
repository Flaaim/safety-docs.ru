<?php

namespace App\Sender\Entity;

use App\Payment\Entity\Email;
use App\Product\Entity\FileInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class Recipient
{
    /** @var ArrayCollection<FileInterface> $attachments */
    private Collection $attachments;
    public function __construct(
        private readonly Email $email,
        private readonly string $subject
    ){
        $this->attachments = new ArrayCollection();
    }

    public function addAttachment(FileInterface $file): void
    {
        if(!$file->exists()){
            throw new \DomainException("File '{$file->getValue()}' does not exists.");
        }
        $this->attachments->add($file);
    }
    /** @return Collection<int, FileInterface> */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }
    public function getSubject(): string
    {
        return $this->subject;
    }
    public function getEmail(): Email
    {
        return $this->email;
    }
}