<?php

namespace App\Product\Command\GetBySlug;

use App\Product\Entity\DTO\ProductDTO;
use App\Product\Entity\ProductRepository;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
    ){
    }

    public function handle(Command $command): ProductDTO
    {
        $product = $this->products->findBySlug($command->slug);
        if(null === $product){
            throw new \DomainException('Product not found.');
        }

        return ProductDTO::fromProduct($product);
    }
}