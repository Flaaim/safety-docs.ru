<?php

namespace App\Direction\Entity\Category\DTO;

class CategoryDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $text,
        public string $slug,
        public string $directionId,
    ){
    }
}