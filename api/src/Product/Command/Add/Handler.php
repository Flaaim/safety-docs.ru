<?php

namespace App\Product\Command\Add;

use App\Flusher;
use App\Product\Entity\Amount;
use App\Product\Entity\Filename;
use App\Product\Entity\FormatDocument;
use App\Product\Entity\Product;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Product\Service\File\FileUploaderInterface;
use App\Shared\Domain\ValueObject\Currency;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher $flusher,
        private readonly FileUploaderInterface   $uploader,
    ){
    }

    public function handle(Command $command): void
    {
        $slug = new Slug($command->slug);
        $product = $this->products->findBySlug($slug);
        if($product) {
            throw new \DomainException("Product with slug " .$command->slug. " already exists.");
        }

        $productId = ProductId::generate();

        if($command->filename !== $command->file->getClientFilename()){
            throw new \DomainException('Filename and name of file name is not equals.');
        }

        $this->uploader->upload($productId->getValue(), $command->file);

        $formatEnums = array_map(
            static fn(string $format) => FormatDocument::from($format),
            $command->formatDocuments
        );

        $product = new Product(
            $productId,
            $command->name,
            new Amount($command->amount, new Currency('RUB')),
            new Filename($command->filename),
            $command->cipher,
            $slug,
            new \DateTimeImmutable($command->updatedAt),
            $command->totalDocuments,
            $formatEnums
        );

        $this->products->add($product);

        $this->flusher->flush();

    }
}