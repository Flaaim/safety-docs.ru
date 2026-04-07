<?php

namespace App\Product\Test\Command\Images\GetAll;

use App\Product\Command\Images\GetAll\Command;
use App\Product\Command\Images\GetAll\Handler;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;

class TestHandler extends TestCase
{
    private ProductRepository $products;
    private Handler $handler;

    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->handler = new Handler($this->products);
    }

    public function testNotFound(): void
    {
        $command = new Command('8d349d57-3f08-4248-82a5-90bc0b8c0f20');
        $productId = new ProductId($command->productId);

        $this->products->expects(self::once())->method('get')
            ->with(self::equalTo($productId))
            ->willThrowException(new \DomainException('Product not found.'));

        self::expectExceptionMessage('Product not found.');
        self::expectException(\DomainException::class);
        $this->handler->handle($command);

    }

    public function testSuccess(): void
    {
        $command = new Command('8d349d57-3f08-4248-82a5-90bc0b8c0f20');
        $productId = new ProductId($command->productId);

        $product = (new ProductBuilder())->withId($productId)->withImages(['image1.jpg', 'image2.jpg', 'image3.jpg'])->build();

        $this->products->expects(self::once())->method('get')
            ->with(self::equalTo($productId))
            ->willReturn($product);

        $result = $this->handler->handle($command);

        self::assertEquals(['image1.jpg', 'image2.jpg', 'image3.jpg'], $result);
    }
}