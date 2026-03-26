<?php

namespace App\Direction\Entity\Category\DTO;

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
                fn(CategoryDTO $category) => [
                    'id' => $category->id,
                    'title' => $category->title,
                    'description' => $category->description,
                    'text' => $category->text,
                    'slug' => $category->slug,
                    'direction_id' => $category->directionId,
                ], $this->categories
            ),
            'total' => $this->total
        ];
    }
}