<?php

namespace App\Direction\Entity\Category\DTO\Admin;

class CategoryPaginatedDTO
{
    public function __construct(
        private readonly array $categories,
        private readonly int $total,
        private readonly int $currentPage,
        private readonly int $perPage,
        private readonly int $totalPages
    ){
    }
    public function getCategories(): array
    {
        return $this->categories;
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