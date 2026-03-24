<?php

namespace App\Product\Command\GetBySlug;

use App\Product\Entity\DTO\ProductDTO;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
    ){
    }

    public function handle(Command $command): ProductDTO
    {
        $slug = new Slug($command->slug);
        $product = $this->products->findBySlug($slug);
        if(null === $product){
            throw new \DomainException('Product not found.');
        }

        return ProductDTO::fromProduct($product);
    }
}