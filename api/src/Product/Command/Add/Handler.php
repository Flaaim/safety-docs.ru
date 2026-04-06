<?php

namespace App\Product\Command\Add;

use App\Flusher;
use App\Product\Command\Upload\Handler as UploadHandler;
use App\Product\Entity\Amount;
use App\Product\Entity\File;
use App\Product\Entity\FormatDocument;
use App\Product\Entity\Product;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Shared\Domain\ValueObject\Currency;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher $flusher,
        private readonly UploadHandler $uploadHandler,
    ){
    }

    public function handle(Command $command): void
    {
        $slug = new Slug($command->slug);
        $product = $this->products->findBySlug($slug);
        if($product) {
            throw new \DomainException("Product with slug " .$command->slug. " already exists.");
        }

        $formatEnums = array_map(
            static fn(string $format) => FormatDocument::from($format),
            $command->formatDocuments
        );

        $product = new Product(
            ProductId::generate(),
            $command->name,
            new Amount($command->amount, new Currency('RUB')),
            $file = new File($command->path),
            $command->cipher,
            $slug,
            new \DateTimeImmutable($command->updatedAt),
            $command->totalDocuments,
            $formatEnums
        );

        $this->products->add($product);

        $this->uploadHandler->handle($file->getValue(), $command->file);

        $this->flusher->flush();

    }
}