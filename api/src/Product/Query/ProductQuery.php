<?php

namespace App\Product\Query;

use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Shared\Domain\Query\ProductQueryDTO;
use App\Shared\Domain\Query\ProductQueryInterface;

class ProductQuery implements ProductQueryInterface
{
    public function __construct(
        private readonly ProductRepository $products
    ){
    }
    public function getProduct(string $id): ProductQueryDTO
    {
        $product = $this->products->get(new ProductId($id));

        return ProductQueryDTO::fromProduct($product);
    }
}