<?php

namespace App\Product\Entity\DTO;

use App\Product\Entity\Product;

class ProductDTO
{
    public function __construct(
        public string $productId,
        public string $name,
        public string $formattedPrice,
    ){
    }

    public static function fromProduct(Product $product): self
    {
        return new self(
            $product->getId()->getValue(),
            $product->getName(),
            $product->getPrice()->formatted()
        );
    }
}