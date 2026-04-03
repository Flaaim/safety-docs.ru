<?php

namespace App\Product\Entity\DTO;

use App\Product\Entity\Product;

class ProductDTO
{
    public function __construct(
        public string $productId,
        public string $name,
        public string $cipher,
        public string $slug,
        public string $formattedPrice,
        public string $updatedAt,
        public string $file
    ){
    }

    public static function fromProduct(Product $product): self
    {
        return new self(
            $product->getId()->getValue(),
            $product->getName(),
            $product->getCipher(),
            $product->getSlug()->getValue(),
            $product->getAmount()->formatted(),
            $product->getUpdatedAt()->format('d.m.Y'),
            $product->getFile()->getValue()
        );
    }
}