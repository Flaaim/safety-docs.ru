<?php

namespace App\Product\Command\Update;

use App\Flusher;
use App\Product\Entity\Amount;
use App\Product\Entity\Filename;
use App\Product\Entity\FormatDocument;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Product\Service\File\FileRemoverInterface;
use App\Product\Service\File\FileUploaderInterface;
use App\Shared\Domain\ValueObject\Currency;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher $flusher,
        private readonly FileUploaderInterface   $uploader,
        private readonly FileRemoverInterface $fileRemover,
    ){
    }

    public function handle(Command $command): void
    {
        $productId = new ProductId($command->productId);
        $product = $this->products->findById($productId);

        if($product === null) {
            throw new \DomainException('Product not found.');
        }

        $slug = new Slug($command->slug);
        $existingProduct = $this->products->findBySlug($slug);

        if ($existingProduct && !$existingProduct->getId()->equals($productId)) {
            throw new \DomainException('Product with this slug already exists.');
        }

        $filename = $product->getFilename()->getValue();

        if($command->file !== null){
            $filename = $command->file->getClientFilename();
        }

        $oldRelativeFilePath = $productId->getValue() . DIRECTORY_SEPARATOR . $product->getFilename()->getValue();

        $formatEnums = array_map(
            static fn(string $format) => FormatDocument::from($format),
            $command->formatDocuments
        );

        $product->update(
            $command->name,
            $command->cipher,
            new Slug($command->slug),
            new Amount($command->amount, new Currency('RUB')),
            new Filename($filename),
            $command->totalDocuments,
            $formatEnums,
            new \DateTimeImmutable($command->updatedAt),
        );


        if ($command->file !== null) {
            $relativeFileDir = $productId->getValue();
            $this->uploader->upload($relativeFileDir, $command->file);
        }

        $this->flusher->flush();

        if($command->file !== null) {
            $this->fileRemover->remove($oldRelativeFilePath);
        }
    }
}