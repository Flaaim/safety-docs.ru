<?php

namespace Test\Functional\Payment;

use App\Product\Entity\Currency;
use App\Product\Entity\File;
use App\Product\Entity\Price;
use App\Product\Entity\Product;
use App\Shared\Domain\ValueObject\Id;

class ProductBuilder
{
    private Id $id;
    private string $name;
    private string $cipher;
    private Price $price;
    private File $file;
    private string $course;

    public function __construct()
    {
        $this->id = new Id("b38e76c0-ac23-4c48-85fd-975f32c8801f");
        $this->name = "Оказание первой помощи пострадавшим";
        $this->cipher = "ОТ 201.18";
        $this->price = new Price(350.00, new Currency('RUB'));
        $this->file = new File("201/ot201.18.docx");
        $this->course = '201';
    }
    public function withId(Id $id): self
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
    public function withPrice(Price $price): self
    {
        $this->price = $price;
        return $this;
    }
    public function withFile(File $file): self
    {
        $this->file = $file;
        return $this;
    }
    public function withCourse(string $course): self
    {
        $this->course = $course;
        return $this;
    }
    public function build(): Product
    {
        return new Product(
            $this->id,
            $this->name,
            $this->price,
            $this->file,
            $this->cipher,
            $this->course
        );
    }
}