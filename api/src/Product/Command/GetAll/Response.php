<?php

namespace App\Product\Command\GetAll;

use App\Product\Entity\DTO\ProductPaginatedDTO;
use App\Product\Entity\Product;

class Response implements \JsonSerializable
{
    private function __construct(
        private readonly array $products,
        private readonly int $total,
        private readonly int $currentPage,
        private readonly int $perPage,
        private readonly int $totalPages,
    ){
    }

    public static function fromResult(ProductPaginatedDTO $paginatedDTO): self
    {
        return new self(
            $paginatedDTO->getProducts(),
            $paginatedDTO->getTotal(),
            $paginatedDTO->getCurrentPage(),
            $paginatedDTO->getPerPage(),
            $paginatedDTO->getTotalPages()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'products' => array_map(fn(Product $product) => [
                'id' => $product->getId()->getValue(),
                'name' => $product->getName(),
                'price' => $product->getPrice()->formatted(),
                'cipher' => $product->getCipher(),
                'file' => $product->getFile()->getValue(),
                'slug' => $product->getSlug(),
            ], $this->products),
            'total' => $this->total,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
            'totalPages' => $this->totalPages,
        ];
    }
}