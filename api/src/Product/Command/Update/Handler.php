<?php

namespace App\Product\Command\Update;

use App\Flusher;
use App\Product\Command\Add\Upload\Handler as UploadHandler;
use App\Product\Entity\Amount;
use App\Product\Entity\File;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Product\Service\File\FileRemoverInterface;
use App\Shared\Domain\ValueObject\Currency;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher $flusher,
        private readonly UploadHandler $uploadHandler,
        private readonly FileRemoverInterface $fileRemover,
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


        $path = $command->file !== null
            ? dirname($product->getFile()->getValue()) . DIRECTORY_SEPARATOR . $command->file->getClientFilename()
            : $product->getFile()->getValue();


        $oldFilePath = $product->getFile()->getValue();

        $product->update(
            $command->name,
            $command->cipher,
            new Slug($command->slug),
            new Amount($command->amount, new Currency('RUB')),
            new File($path),
            new \DateTimeImmutable($command->updatedAt),
        );


        if ($command->file !== null) {
            $this->uploadHandler->handle($path, $command->file);
        }

        $this->flusher->flush();

        if($command->file !== null) {
            $this->fileRemover->remove($oldFilePath);
        }
    }
}