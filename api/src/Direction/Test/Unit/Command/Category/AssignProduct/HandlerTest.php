<?php

namespace App\Direction\Test\Unit\Command\Category\AssignProduct;

use App\Direction\Command\Direction\Category\AssignProduct\Command;
use App\Direction\Command\Direction\Category\AssignProduct\Handler;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Test\Builder\CategoryBuilder;
use App\Flusher;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    private ProductRepository $products;
    private CategoryRepository $categories;
    private Flusher $flusher;
    private Handler $handler;

    public function setUp(): void
    {
        $this->products = $this->createMock(ProductRepository::class);
        $this->categories = $this->createMock(CategoryRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->handler = new Handler($this->products,$this->categories, $this->flusher);
    }
    public function testCategoryNotFound(): void
    {
        $command = new Command('534f82af-22ba-4899-8508-1e4f17f17224', '2fbb615f-54d0-4233-98f2-3c438e5b0ae7');
        $categoryId = new CategoryId($command->categoryId);

        $this->categories->expects(self::once())->method('findById')
            ->with($categoryId)
            ->willReturn(null);

        $this->products->expects(self::never())->method('findById');
        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category not found.');

        $this->handler->handle($command);
    }
    public function testProductNotFound(): void
    {
        $command = new Command('534f82af-22ba-4899-8508-1e4f17f17224', '2fbb615f-54d0-4233-98f2-3c438e5b0ae7');
        $productId = new ProductId($command->productId);
        $categoryId = new CategoryId($command->categoryId);

        $category = (new CategoryBuilder())->withCategoryId($categoryId)->build();

        $this->categories->expects(self::once())->method('findById')
            ->with($categoryId)
            ->willReturn($category);

        $this->products->expects(self::once())->method('findById')
            ->with($productId)
            ->willReturn(null);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product not found.');
        $this->handler->handle($command);
    }

    public function testAssignProduct(): void
    {
        $command = new Command('534f82af-22ba-4899-8508-1e4f17f17224', '2fbb615f-54d0-4233-98f2-3c438e5b0ae7');
        $productId = new ProductId($command->productId);
        $categoryId = new CategoryId($command->categoryId);

        $product = (new ProductBuilder())->withId($productId)->build();
        $category = (new CategoryBuilder())->withCategoryId(($categoryId))->build();

        $this->products->expects(self::once())->method('findById')
            ->with($productId)
            ->willReturn($product);

        $this->categories->expects(self::once())->method('findById')
            ->with($categoryId)
            ->willReturn($category);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($product, $category->getProduct());
    }

    public function testAssignedProductAlready(): void
    {
        $command = new Command('534f82af-22ba-4899-8508-1e4f17f17224', '2fbb615f-54d0-4233-98f2-3c438e5b0ae7');
        $productId = new ProductId($command->productId);
        $categoryId = new CategoryId($command->categoryId);

        $product = (new ProductBuilder())->withId($productId)->build();
        $category = (new CategoryBuilder())
            ->withCategoryId(($categoryId))
            ->withProduct($product)
            ->build();

        $this->products->expects(self::once())->method('findById')
            ->with($productId)
            ->willReturn($product);

        $this->categories->expects(self::once())->method('findById')
            ->with($categoryId)
            ->willReturn($category);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product already assigned. You must delete it first.');

        $this->handler->handle($command);
    }
}