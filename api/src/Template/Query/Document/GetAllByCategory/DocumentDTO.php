<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetAllByCategory;

final class DocumentDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly float $amount,
        public readonly string $filename,
        public readonly string $slug,
        public readonly string $createdAt
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            amount: (float) $data['amount'],
            filename: $data['filename'],
            slug: $data['slug'],
            createdAt: $data['createdAt']
        );
    }
}
