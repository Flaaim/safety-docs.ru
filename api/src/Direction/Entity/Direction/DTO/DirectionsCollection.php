<?php

namespace App\Direction\Entity\Direction\DTO;

use App\Direction\Entity\Category\Category;

class DirectionsCollection implements \JsonSerializable
{
    public function __construct(
        public readonly array $directions,
        public readonly int $total,
    ){
    }

    public function jsonSerialize(): array
    {
        return [
            'directions' => array_map(
                fn($direction) => [
                    'id' => $direction->getId()->getValue(),
                    'title' => $direction->getTitle(),
                    'description' => $direction->getDescription(),
                    'text' => $direction->getText(),
                    'slug' => $direction->getSlug()->getValue(),
                    'categories' => array_map(
                        fn(Category $category) => [
                            'title' => $category->getTitle(),
                            'description' => $category->getDescription(),
                            'text' => $category->getText(),
                            'slug' => $category->getSlug()->getValue(),
                        ], $direction->getCategories()->toArray()
                    )
                ], $this->directions
            ),
            'total' => $this->total,
        ];
    }
}