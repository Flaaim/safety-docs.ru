<?php

namespace App\Product\Test;

use App\Product\Entity\Filename;
use App\Product\Entity\Amount;
use App\Product\Entity\FormatDocument;
use App\Product\Entity\Product;
use App\Product\Entity\ProductId;
use App\Product\Entity\Slug;
use App\Shared\Domain\ValueObject\Currency;

class ProductBuilder
{
    private ProductId $id;
    private string $name;
    private string $cipher;
    private Amount $price;
    private Filename $filename;
    private Slug $slug;
    private int $totalDocuments;
    private array $formatDocument;
    private array $images;
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = new ProductId("b38e76c0-ac23-4c48-85fd-975f32c8801f");
        $this->name = "Оказание первой помощи пострадавшим";
        $this->cipher = "ОТ 201.18";
        $this->price = new Amount(350.00, new Currency('RUB'));
        $this->filename = new Filename("ot201.18.rar");
        $this->slug = new Slug("201");
        $this->updatedAt = new \DateTimeImmutable();
        $this->totalDocuments = 22;
        $this->formatDocument = [FormatDocument::DOCX, FormatDocument::PDF];
        $this->images = [];
    }
    public function withId(ProductId $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function withCipher(string $cipher): self
    {
        $this->cipher = $cipher;
        return $this;
    }
    public function withPrice(Amount $price): self
    {
        $this->price = $price;
        return $this;
    }
    public function withFilename(Filename $filename): self
    {
        $this->filename = $filename;
        return $this;
    }
    public function withSlug(Slug $slug): self
    {
        $this->slug = $slug;
        return $this;
    }
    public function withUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    public function withTotalDocuments(int $totalDocuments): self
    {
        $this->totalDocuments = $totalDocuments;
        return $this;
    }
    public function withFormatDocument(array $formatDocument): self
    {
        $this->formatDocument = $formatDocument;
        return $this;
    }
    public function withImages(array $images): self
    {
        $this->images = $images;
        return $this;
    }
    public function build(): Product
    {
        $product = new Product(
            $this->id,
            $this->name,
            $this->price,
            $this->filename,
            $this->cipher,
            $this->slug,
            $this->updatedAt,
            $this->totalDocuments,
            $this->formatDocument
        );

        if(!empty($this->images)) {
            foreach ($this->images as $image) {
                $product->attachImage($image);
            }
        }
        return $product;
    }
}