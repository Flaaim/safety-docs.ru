<?php

namespace App\Direction\Entity\Direction\DTO;

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
                    'title' => $direction->getTitle(),
                    'description' => $direction->getDescription(),
                    'text' => $direction->getText(),
                    'slug' => $direction->getSlug()->getValue(),
                    'categories' => array_map(
                        fn($category) => [
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