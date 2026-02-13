<?php

namespace App\Product\Entity\DTO;

class ProductDTO
{
    public function __construct(
        public string $name,
        public string $formattedPrice,
    ){
    }
}