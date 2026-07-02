<?php

namespace App\Shared\Domain\Query;

use App\Template\Entity\Document\Document;

class DocumentQueryDTO
{
    private function __construct(
        public string $id,
        public float $amount,
    ) {}

    public static function fromDocument(Document $document): self
    {
        return new self(
            $document->getId()->getValue(),
            $document->getAmount()->getValue(),
        );
    }
}
