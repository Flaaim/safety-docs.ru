<?php

namespace App\Direction\Entity\Category\DTO;

use App\Direction\Entity\Category\Category;

class CategoriesCollection implements \JsonSerializable
{
    public function __construct(
        public readonly array $categories,
        public readonly int $total,
    ){
    }

    public function jsonSerialize(): array
    {
        return [
            'categories' => array_map(
                fn(Category $category) => [
                    'title' => $category->getTitle(),
                    'description' => $category->getDescription(),
                    'text' => $category->getText(),
                    'slug' => $category->getSlug()->getValue(),
                    'direction_id' => $category->getDirection()->getId()->getValue(),
                ], $this->categories
            ),
            'total' => $this->total
        ];
    }
}