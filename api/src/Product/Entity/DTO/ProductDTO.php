<?php

namespace App\Product\Entity\DTO;

use App\Product\Entity\FormatDocument;
use App\Product\Entity\Product;

class ProductDTO
{
    public function __construct(
        public string $productId,
        public string $name,
        public string $cipher,
        public string $slug,
        public string $formattedPrice,
        public string $updatedAt,
        public string $file,
        public int $totalDocuments,
        public array $formatDocuments,
    ){
    }

    public static function fromProduct(Product $product): self
    {

        return new self(
            $product->getId()->getValue(),
            $product->getName(),
            $product->getCipher(),
            $product->getSlug()->getValue(),
            $product->getAmount()->formatted(),
            $product->getUpdatedAt()->format('d.m.Y'),
            $product->getFile()->getValue(),
            $product->getTotalDocuments(),
            array_map(function (FormatDocument $document) {
                return $document->value;
            }, $product->getFormatDocuments()),
        );
    }
}