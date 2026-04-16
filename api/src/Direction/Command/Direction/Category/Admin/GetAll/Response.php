<?php

namespace App\Direction\Command\Direction\Category\Admin\GetAll;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\DTO\Admin\CategoryPaginatedDTO;
use App\Direction\Entity\Category\DTO\CategoryDTO;
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
            'categories' => array_map(fn(CategoryDTO $category) => [
                'id' => $category->id,
                'title' => $category->title,
                'description' => $category->description,
                'text' => $category->text,
                'slug' => $category->slug,
                'directionTitle' => $category->directionTitle,
                'directionId' => $category->directionId,
                'productTitle' => $category->productTitle,
                'productId' => $category->productId,
            ], $this->categories),
            'total' => $this->total,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
            'totalPages' => $this->totalPages,
        ];
    }
}