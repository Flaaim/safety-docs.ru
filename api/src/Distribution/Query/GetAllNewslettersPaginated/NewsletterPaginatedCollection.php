<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetAllNewslettersPaginated;

final class NewsletterPaginatedCollection implements \JsonSerializable
{
    public function __construct(
        public readonly array $newsletters,
        public readonly int $total,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly int $totalPages
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'newsletters' => array_map(fn($newsletter) => [
                'subject' => $newsletter['id'],
                'templateId' => $newsletter['template_id'],
            ], $this->newsletters),
            'total' => $this->total,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
            'totalPages' => $this->totalPages,

        ];
    }
}