<?php

namespace App\Direction\Test\Unit\Command\Category\RefuseProduct;

use App\Direction\Command\Direction\Category\RefuseProduct\Command;
use App\Direction\Command\Direction\Category\RefuseProduct\Handler;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\CategoryBuilder;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Flusher;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    /** @var CategoryRepository&MockObject  */
    private CategoryRepository $categories;
    /** @var Flusher&MockObject  */
    private Flusher $flusher;
    private Handler $handler;

    public function setUp(): void
    {
        $this->categories = $this->createMock(CategoryRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->handler = new Handler($this->categories, $this->flusher);
    }

    public function testCategoryNotFound(): void
    {
        $command = new Command('534f82af-22ba-4899-8508-1e4f17f17224');
        $categoryId = new CategoryId($command->categoryId);

        $this->categories->expects(self::once())->method('findById')
            ->with($categoryId)
            ->willReturn(null);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category not found.');

        $this->handler->handle($command);
    }

    public function testRefuseProduct(): void
    {
        $command = new Command('534f82af-22ba-4899-8508-1e4f17f17224');
        $productId = new ProductId('2fbb615f-54d0-4233-98f2-3c438e5b0ae7');
        $categoryId = new CategoryId($command->categoryId);

        $direction = (new DirectionBuilder())->build();
        $product = (new ProductBuilder())->withId($productId)->build();
        $category = (new CategoryBuilder())
            ->withCategoryId(($categoryId))
            ->withProduct($product)
            ->withParent((new CategoryBuilder())->withSlug(new Slug('parent'))->build($direction))
            ->build($direction);


        $this->categories->expects(self::once())->method('findById')
            ->with($categoryId)
            ->willReturn($category);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals(null, $category->getProduct());
    }

    public function testRefuseNotAssignedProduct(): void
    {
        $command = new Command('534f82af-22ba-4899-8508-1e4f17f17224');
        $categoryId = new CategoryId($command->categoryId);
        $direction = (new DirectionBuilder())->build();
        $category = (new CategoryBuilder())
            ->withCategoryId(($categoryId))
            ->build($direction);

        $this->categories->expects(self::once())->method('findById')
            ->with($categoryId)
            ->willReturn($category);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product not assigned.');

        $this->handler->handle($command);
    }
}