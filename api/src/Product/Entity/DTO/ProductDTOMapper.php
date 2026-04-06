<?php

namespace App\Product\Entity\DTO;

use App\Product\Entity\Product;

class ProductDTOMapper
{
    public function map(?Product $product): ?ProductDTO
    {
        if(null === $product) {
            return null;
        }
        return ProductDTO::fromProduct($product);
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