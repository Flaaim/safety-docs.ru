<?php

namespace App\Product\Entity\DTO;

class ProductPaginatedDTO
{
    public function __construct(
        private readonly array $products,
        private readonly int $total,
        private readonly int $currentPage,
        private readonly int $perPage,
        private readonly int $totalPages
    ){
    }

    public function getProducts(): array
    {
        return $this->products;
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