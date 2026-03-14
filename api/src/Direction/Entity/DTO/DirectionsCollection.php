<?php

namespace App\Direction\Entity\DTO;

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
                    'slug' => $direction->getSlug()->getValue(),
                ], $this->directions
            ),
            'total' => $this->total,
        ];
    }
}