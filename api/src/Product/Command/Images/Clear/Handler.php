<?php

namespace App\Product\Command\Images\Clear;

use App\Flusher;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Service\File\FileRemoverInterface;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher            $flusher,
        private readonly FileRemoverInterface  $fileRemover,
    ){
    }
    public function handle(Command $command): void
    {
        $product = $this->products->findById(new ProductId($command->productId));
        if($product === null) {
            throw new \DomainException('Product not found.');
        }

        $imagesToRemove = $product->getImages();
        $product->clearImages();

        $this->flusher->flush();

        foreach ($imagesToRemove as $image) {
            $oldRelativeImagePath = $product->getId()->getValue() . DIRECTORY_SEPARATOR . $image;
            $this->fileRemover->remove($oldRelativeImagePath);
        }
    }
}