<?php

namespace App\Direction\Entity\Direction\DTO;

class DirectionDTO
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $description,
        public string $text,
    ){
    }

}