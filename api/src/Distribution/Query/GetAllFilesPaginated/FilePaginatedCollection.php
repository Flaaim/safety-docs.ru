<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetAllFilesPaginated;

final class FilePaginatedCollection implements \JsonSerializable
{
    public function __construct(
        public readonly array $files,
        public readonly int $total,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly int $totalPages
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
                'files' => array_map(fn ($file) => [
                    'id' => $file['id'],
                    'name' => $file['name'],
                    'date' => (new \DateTimeImmutable($file['date']))->format('Y-m-d'),
                    'complete' => (bool)$file['complete'],
                ], $this->files),
                'total' => $this->total,
                'currentPage' => $this->currentPage,
                'perPage' => $this->perPage,
                'totalPages' => $this->totalPages,
            ];
    }
}
