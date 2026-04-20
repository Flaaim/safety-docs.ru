<?php

namespace App\Product\Command\Images\GetAll;

use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly FileSystemPathInterface $fileSystemPath
    ){
    }

    public function handle(Command $command): array
    {
        $product = $this->products->get(new ProductId($command->productId));

        $images = $product->getImages();

        $imagesWithPath = [];

        foreach ($images as $image) {
            $imagesWithPath[] = $this->fileSystemPath->getValue() .
                DIRECTORY_SEPARATOR . $product->getId()->getValue() .
                DIRECTORY_SEPARATOR . $image;
        }
        return $imagesWithPath;
    }
}