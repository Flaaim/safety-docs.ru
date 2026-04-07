<?php

namespace App\Product\Command\Images\GetAll;

use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
    ){
    }

    public function handle(Command $command): array
    {
        $product = $this->products->get(new ProductId($command->productId));

        return $product->getImages();
    }
}