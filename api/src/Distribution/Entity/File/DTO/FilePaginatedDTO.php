<?php

declare(strict_types=1);

namespace App\Distribution\Entity\File\DTO;

final class FilePaginatedDTO
{
    public function __construct(
        private readonly array $files,
        private readonly int $total,
        private readonly int $currentPage,
        private readonly int $perPage,
        private readonly int $totalPages
    ) {
    }

    public function getFiles(): array
    {
        return $this->files;
    }
    public function getTotal(): int
    {
        return $this->total;
    }
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }
    public function getPerPage(): int
    {
        return $this->perPage;
    }
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }
}
