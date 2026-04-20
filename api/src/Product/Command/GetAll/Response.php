<?php

namespace App\Product\Command\GetAll;

use App\Product\Entity\DTO\ProductDTO;
use App\Product\Entity\DTO\ProductPaginatedDTO;

class Response implements \JsonSerializable
{
    /** @var array<ProductDTO> $products */
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
            'products' => array_map(fn(ProductDTO $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'formattedPrice' => $product->formattedPrice,
                'cipher' => $product->cipher,
                'updatedAt' => $product->updatedAt,
                'filename' => $product->filename,
                'images' => $product->images,
            ], $this->products),
            'total' => $this->total,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
            'totalPages' => $this->totalPages,
        ];
    }
}