<?php

namespace App\Product\Command\Get;

use App\Product\Entity\DTO\ProductDTO;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;

class Handler
{

    public function __construct(
        private ProductRepository $products
    ){
    }

    public function handle(Command $command): ProductDTO
    {
        $product = $this->products->get(new ProductId($command->productId));

        return new ProductDTO(
            $product->getName(),
            $product->getPrice()->formatted()
        );
    }
}