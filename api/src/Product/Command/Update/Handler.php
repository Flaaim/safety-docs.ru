<?php

namespace App\Product\Command\Update;

use App\Flusher;
use App\Product\Entity\Amount;
use App\Product\Entity\File;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Shared\Domain\ValueObject\Currency;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher $flusher,
    ){
    }

    public function handle(Command $command): void
    {
        $productId = new ProductId($command->productId);
        $product = $this->products->get($productId);

        $slug = new Slug($command->slug);
        $existingProduct = $this->products->findBySlug($slug);

        if ($existingProduct && !$existingProduct->getId()->equals($productId)) {
            throw new \DomainException('Product with this slug already exists.');
        }

        $product->update(
            $command->name,
            new Amount($command->amount, new Currency('RUB')),
            new File($command->path),
            $command->cipher,
            new Slug($command->slug),
            new \DateTimeImmutable($command->updatedAt),
        );

        $this->flusher->flush();
    }
}