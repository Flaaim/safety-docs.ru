<?php

namespace App\Direction\Test\Unit\Command\Category\Delete;


use App\Direction\Command\Direction\Category\Delete\Command;
use App\Direction\Command\Direction\Category\Delete\Handler;
use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Direction\Direction;
use App\Direction\Test\Builder\CategoryBuilder;
use App\Flusher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    /** @var CategoryRepository&MockObject  */
    private CategoryRepository $categories;
    /** @var Flusher&MockObject  */
    private Flusher $flusher;
    private Handler  $handler;
    public function setUp(): void
    {
        $this->categories = $this->createMock(CategoryRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->handler = new Handler($this->categories, $this->flusher);
    }
    public function testNotFound(): void
    {
        $command = new Command('2f076431-c2e5-4203-bedb-aa847792c2ba');
        $categoryId = new CategoryId($command->categoryId);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn(null);

        $this->categories->expects($this->never())->method('remove');
        $this->flusher->expects($this->never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category not found.');
        $this->handler->handle($command);
    }

    public function testCanNotBeDeleted(): void
    {
        $command = new Command('2f076431-c2e5-4203-bedb-aa847792c2ba');
        $categoryId = new CategoryId($command->categoryId);

        $direction = $this->createMock(Direction::class);
        $category = (new CategoryBuilder())
            ->withChildren([$this->createMock(Category::class)])
            ->build($direction);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn($category);

        $this->categories->expects($this->never())->method('remove');
        $this->flusher->expects($this->never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category cannot be deleted. It has children.');
        $this->handler->handle($command);
    }

    public function testSuccess(): void
    {
        $command = new Command('2f076431-c2e5-4203-bedb-aa847792c2ba');
        $categoryId = new CategoryId($command->categoryId);

        $direction = $this->createMock(Direction::class);
        $category = (new CategoryBuilder())->build($direction);
        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn($category);

        $this->categories->expects($this->once())->method('remove');
        $this->flusher->expects($this->once())->method('flush');

        $this->handler->handle($command);
    }
}