<?php

declare(strict_types=1);

namespace App\Distribution\Command\GetContactFiles;

use App\Distribution\Entity\File\DTO\FileDTO;
use App\Distribution\Entity\File\DTO\FilePaginatedDTO;

final class Response implements \JsonSerializable
{
    private function __construct(
        private readonly array $files,
        private readonly int $total,
        private readonly int $currentPage,
        private readonly int $perPage,
        private readonly int $totalPages,
    ) {
    }

    public static function fromResult(FilePaginatedDTO $paginatedDTO): self
    {
        return new self(
            $paginatedDTO->getFiles(),
            $paginatedDTO->getTotal(),
            $paginatedDTO->getCurrentPage(),
            $paginatedDTO->getPerPage(),
            $paginatedDTO->getTotalPages()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'files' => array_map(fn(FileDTO $file) => [
                'id' => $file->id,
                'name' => $file->name,
                'date' => $file->date,
                'complete' => $file->complete,
            ], $this->files),
            'total' => $this->total,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
            'totalPages' => $this->totalPages,
        ];
    }
}
