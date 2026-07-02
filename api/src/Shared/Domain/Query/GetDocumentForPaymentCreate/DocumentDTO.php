<?php

namespace App\Shared\Domain\Query\GetDocumentForPaymentCreate;

class DocumentDTO
{
    private function __construct(
        public string $id,
        public float $amount,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['amount'],
        );
    }
}
