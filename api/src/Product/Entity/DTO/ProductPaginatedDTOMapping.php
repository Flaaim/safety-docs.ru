<?php

namespace App\Product\Entity\DTO;


class ProductPaginatedDTOMapping
{
    public function map(
        array $products,
        int $perPage,
        int $page
    ): ProductPaginatedDTO
    {
        $total = count($products);
        $offset = ($page - 1) * $perPage;
        $slicedProducts = array_slice($products, $offset, $perPage);

        return new ProductPaginatedDTO(
            $slicedProducts,
            $total,
            $page,
            $perPage,
            (int) ceil($total / $perPage) ?: 1
        );
    }
}