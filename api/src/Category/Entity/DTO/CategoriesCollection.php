<?php

namespace App\Category\Entity\DTO;

use App\Category\Entity\Category;

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
                    'direction_id' => $category->getDirectionId()
                ], $this->categories
            ),
            'total' => $this->total
        ];
    }
}