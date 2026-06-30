<?php

declare(strict_types=1);

namespace App\Template\Entity\Document;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'documents')]
final class Document
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'document_id')]
        private DocumentId $id,
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,
        #[ORM\Column(type: 'document_amount')]
        private Amount $amount,
        #[ORM\Column(type: 'filename')]
        private Filename $filename,
        private string $slug,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $createdAt
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): DocumentId
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getAmount(): Amount
    {
        return $this->amount;
    }
    public function getFilename(): Filename
    {
        return $this->filename;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
