<?php

namespace App\Product\Test\Entity;

use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testAttachImages(): void
    {
        $product = (new ProductBuilder())->build();

        $product->attachImage('image.jpg');
        $product->attachImage('image1.jpg');

        self::assertCount(2, $product->getImages());
    }

    public function testRemoveImage(): void
    {
        $product = (new ProductBuilder())->build();
        $product->attachImage('image.jpg');
        $product->attachImage('image1.jpg');

        $product->removeImage('image.jpg');
        self::assertCount(1, $product->getImages());

        self::assertEquals('image1.jpg', $product->getImages()[0]);
    }

    public function testClearImages(): void
    {
        $product = (new ProductBuilder())->build();
        $product->attachImage('image.jpg');
        $product->attachImage('image1.jpg');

        $product->clearImages();
        self::assertCount(0, $product->getImages());
    }
}