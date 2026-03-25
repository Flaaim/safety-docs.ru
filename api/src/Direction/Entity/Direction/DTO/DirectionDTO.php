<?php

namespace App\Direction\Entity\Direction\DTO;

class DirectionDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $text,
        public string $slug,
        public array $categories,
    ){
    }

}