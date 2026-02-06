<?php

namespace App\Product\Entity;

class ProductDTO
{
    public string $name;
    public float $price;
    public int $quantity;
    public string $format;

    public function __construct(Product $product)
    {
        $this->name = $product->getName();
        $this->price = $product->getPrice()->getValue();
        $this->format = 'docx, pdf';
        $this->quantity = 49;
    }
}