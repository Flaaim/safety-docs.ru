<?php

declare(strict_types=1);

namespace App\Template\Test\Builder;

use App\Shared\Domain\ValueObject\Currency;
use App\Template\Entity\Document\Amount;
use App\Template\Entity\Document\Document;
use App\Template\Entity\Document\DocumentId;
use App\Template\Entity\Document\Filename;
use App\Template\Entity\Slug;
use Ramsey\Uuid\Uuid;

final class DocumentBuilder
{
    public DocumentId $id;
    public string $name;
    public Amount $amount;
    public Filename $filename;
    public string $slug;
    public \DateTimeImmutable $createdAt;

    public function __construct() {
        $this->id = new DocumentId('8c7d4de5-4c64-4cca-b627-f2f6a9d49fef');
        $this->name = 'Инструкция по охране труда при работе на высоте';
        $this->amount = new Amount(200.00, new Currency('RUB'));
        $this->filename = new Filename(Uuid::uuid4()->toString(). '.docx');
        $this->slug = Slug::generate($this->name)->getValue();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function withId(DocumentId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }
    public function withAmount(Amount $amount): self
    {
        $clone = clone $this;
        $clone->amount = $amount;
        return $clone;
    }
    public function withFilename(Filename $filename): self
    {
        $clone = clone $this;
        $clone->filename = $filename;
        return $clone;
    }
    public function withSlug(string $slug): self
    {
        $clone = clone $this;
        $this->slug = $slug;
        return $clone;
    }


    public function build(): Document
    {
        return new Document(
            $this->id,
            $this->name,
            $this->amount,
            $this->filename,
            $this->slug,
            $this->createdAt
        );
    }
}