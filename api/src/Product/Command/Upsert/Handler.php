<?php

namespace App\Product\Command\Upsert;

use App\Flusher;
use App\Product\Entity\Currency;
use App\Product\Entity\File;
use App\Product\Entity\Price;
use App\Product\Entity\Product;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use Ramsey\Uuid\Uuid;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher $flusher,
    )
    {}
    public function handle(Command $command): Response
    {
        $product = $this->products->findBySlug($command->slug);

        if($product) {
            $product->update(
                $command->name,
                new Price($command->amount, new Currency('RUB')),
                new File($command->path),
                $command->cipher,
            );
        }else{
            $product = new Product(
                new ProductId(Uuid::uuid4()->toString()),
                $command->name,
                new Price($command->amount, new Currency('RUB')),
                new File($command->path),
                $command->cipher,
                $command->slug
            );
        }

        $this->products->upsert($product);

        $this->flusher->flush();

        return new Response($product->getId()->getValue());
    }
}