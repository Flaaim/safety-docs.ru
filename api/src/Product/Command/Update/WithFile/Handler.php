<?php

namespace App\Product\Command\Update\WithFile;

use App\Flusher;
use App\Product\Command\Add\Upload\Handler as UploadHandler;
use App\Product\Command\Update\Command;
use App\Product\Command\Update\UpdateProductHandlerInterface;
use App\Product\Entity\Amount;
use App\Product\Entity\File;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Shared\Domain\ValueObject\Currency;

class Handler implements UpdateProductHandlerInterface
{
    const TYPE = "file";

    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher $flusher,
        private readonly UploadHandler $uploadHandler,
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

        $path = dirname($product->getFile()->getValue()) . DIRECTORY_SEPARATOR . $command->file->getClientFilename();

        $product->update(
            $command->name,
            $command->cipher,
            new Slug($command->slug),
            new Amount($command->amount, new Currency('RUB')),
            new File($path),
            new \DateTimeImmutable($command->updatedAt),
        );

        $this->uploadHandler->handle($product->getFile()->getValue(), $command->file);

        $this->flusher->flush();
    }

    public function getType(string $type): bool
    {
        return self::TYPE === $type;
    }
}