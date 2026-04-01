<?php

namespace App\Direction\Command\Direction\Category\Admin\GetAll;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\DTO\Admin\CategoryPaginatedDTO;
use App\Product\Entity\DTO\ProductDTOMapper;

class Response implements \JsonSerializable
{
    private function __construct(
        private readonly array $categories,
        private readonly int $total,
        private readonly int $currentPage,
        private readonly int $perPage,
        private readonly int $totalPages,
    ){
    }

    public static function fromResult(CategoryPaginatedDTO $paginatedDTO): self
    {
        return new self(
            $paginatedDTO->getCategories(),
            $paginatedDTO->getTotal(),
            $paginatedDTO->getCurrentPage(),
            $paginatedDTO->getPerPage(),
            $paginatedDTO->getTotalPages()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'categories' => array_map(fn(Category $category) => [
                'id' => $category->getId()->getValue(),
                'title' => $category->getTitle(),
                'description' => $category->getDescription(),
                'text' => $category->getText(),
                'slug' => $category->getSlug()->getValue(),
                'directionTitle' => $category->getDirection()->getTitle(),
                'directionId' => $category->getDirection()->getId()->getValue(),
                'product' => (new ProductDTOMapper())->map($category->getProduct())
            ], $this->categories),
            'total' => $this->total,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
            'totalPages' => $this->totalPages,
        ];
    }
}