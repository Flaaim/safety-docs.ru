<?php

namespace App\Product\Entity\DTO;

use App\Product\Entity\Product;

class ProductDTOMapper
{
    public function map(Product $product): ProductDTO
    {
        return new ProductDTO(
            $product->getId()->getValue(),
            $product->getName(),
            $product->getCipher(),
            $product->getAmount()->formatted(),
            $product->getUpdatedAt()->format('d.m.Y'),
            $product->getFile()->getValue()
        );
    }

    public function mapCollection(array $products): array
    {
        $result = [];
        foreach ($products as $product) {
            $result[] = $this->map($product);
        }
        return $result;
    }
}