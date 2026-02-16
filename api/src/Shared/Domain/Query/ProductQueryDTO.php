<?php

namespace App\Shared\Domain\Query;

use App\Product\Entity\Product;

class ProductQueryDTO
{
    public readonly string $id;
    public readonly float $amount;
    public readonly string $cipher;

    private function __construct(string $id, float $amount, string $cipher)
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->cipher = $cipher;
    }

    public static function fromProduct(Product $product): self
    {
        return new self(
            $product->getId()->getValue(),
            $product->getAmount()->getValue(),
            $product->getCipher(),
        );
    }

}