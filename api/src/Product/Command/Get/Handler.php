<?php

namespace App\Product\Command\Get;

use App\Product\Entity\DTO\ProductDTO;
use App\Product\Entity\ProductRepository;
use App\Shared\Domain\ValueObject\Id;

class Handler
{

    public function __construct(
        private ProductRepository $products
    ){
    }

    public function handle(Command $command): ProductDTO
    {
        $product = $this->products->get(new Id($command->productId));

        return new ProductDTO(
            $product->getName(),
            $product->getPrice()->formatted()
        );
    }
}