<?php

namespace App\Product\Test\Command\Update;

use App\Flusher;
use App\Product\Command\Update\Command;
use App\Product\Command\Update\Handler;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Entity\Slug;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    private ProductRepository $products;
    private Flusher $flusher;
    private Handler $handler;
    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->handler = new Handler($this->products, $this->flusher);
    }

    public function testSuccess(): void
    {
        $slug = new Slug('education');
        $command = $this->createCommand($slug);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');
        $product = (new ProductBuilder())->withId($productId)->build();

        $this->products->expects(self::once())->method('get')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn(null);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);
    }

    public function testExistsAnotherProductWithSlug(): void
    {
        $slug = new Slug('education');
        $command = $this->createCommand($slug);

        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');
        $product = (new ProductBuilder())->withId($productId)->build();


        $existingProduct = (new ProductBuilder())->withSlug($slug)->build();

        $this->products->expects(self::once())->method('get')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($existingProduct);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product with this slug already exists.');

        $this->handler->handle($command);
    }
    public function testProductTheSame(): void
    {
        $slug = new Slug('education');
        $command = $this->createCommand($slug);
        $productId = new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d');

        $product = (new ProductBuilder())->withSlug($slug)->withId($productId)->build();

        $this->products->expects(self::once())->method('get')
            ->with($this->equalTo($productId))
            ->willReturn($product);

        $this->products->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($product);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals('Обучение по охране труда - комплект документов', $product->getName());
        self::assertEquals('edu300.1', $product->getCipher());
        self::assertEquals(550.00, $product->getAmount()->getValue());
        self::assertEquals('education', $product->getSlug()->getValue());
    }
    private function createCommand(Slug $slug): Command
    {
        return new Command(
            new ProductId('876675c9-6dfb-4db5-bc90-72b73b75616d'),
            'Обучение по охране труда - комплект документов',
            'edu300.1',
            550.00,
            'safety/education',
            $slug->getValue(),
            (new \DateTimeImmutable())->format('d.m.Y')
        );
    }
}