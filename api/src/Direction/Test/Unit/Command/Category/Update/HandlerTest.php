<?php

namespace App\Direction\Test\Unit\Command\Category\Update;

use App\Direction\Command\Direction\Category\Update\Command;
use App\Direction\Command\Direction\Category\Update\Handler;
use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Flusher;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    private CategoryRepository $categories;
    private DirectionRepository $directions;
    private Handler $handler;
    private Flusher $flusher;
    public function setUp(): void
    {
        $this->categories = $this->createMock(CategoryRepository::class);
        $this->directions = $this->createMock(DirectionRepository::class);

        $this->flusher = $this->createMock(Flusher::class);
        $this->handler = new Handler($this->categories, $this->directions, $this->flusher);
    }

    public function testDirectionNotFound(): void
    {
        $command = $this->getCommand();
        $directionId = new DirectionId($command->directionId);

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn(null);

        $this->categories->expects(self::never())->method('findById');
        $this->categories->expects(self::never())->method('findBySlug');
        $this->flusher->expects(self::never())->method('flush');

        self::expectExceptionMessage('Direction not found.');
        self::expectException(\DomainException::class);
        $this->handler->handle($command);
    }

    public function testCategoryNotFound(): void
    {
        $command = $this->getCommand();
        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->build();

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);


        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn(null);

        $this->categories->expects(self::never())->method('findBySlug');
        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category not found.');

        $this->handler->handle($command);
    }

    public function testSuccessWithNewSlug(): void
    {
        $command = $this->getCommand();

        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->withId($directionId)->build();

        $category =  new Category(
            new CategoryId($command->categoryId),
            'Пожарная безопасность',
            'Комплект документов',
            'Some text',
            new Slug('old-slug'),
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn($category);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($command->title, $category->getTitle());
        self::assertEquals($command->description, $category->getDescription());
        self::assertEquals($command->slug, $category->getSlug()->getValue());
        self::assertEquals($command->text, $category->getText());
    }
    public function testSuccessWithSameSlug(): void
    {
        $command = $this->getCommand();

        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->withId($directionId)->build();

        $category =  new Category(
            new CategoryId($command->categoryId),
            'Пожарная безопасность',
            'Комплект документов',
            'Some text',
            new Slug('service'),
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn($category);

        $this->categories->expects(self::once())->method('findBySlug')
            ->with($this->equalTo(new Slug($command->slug)), $this->equalTo($directionId))
            ->willReturn($category);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($command->title, $category->getTitle());
        self::assertEquals($command->description, $category->getDescription());
        self::assertEquals($command->slug, $category->getSlug()->getValue());
        self::assertEquals($command->text, $category->getText());
    }

    public function testAlreadyTakenAnotherCategory(): void
    {
        $command = $this->getCommand();
        $slug = new Slug($command->slug);
        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->withId($directionId)->build();

        $category =  new Category(
            new CategoryId($command->categoryId),
            'Пожарная безопасность',
            'Комплект документов',
            'Some text',
            new Slug('old-slug'),
            $direction
        );

        $anotherCategory =  new Category(
            new CategoryId('7f2cf3f7-9f47-4d04-ae5e-d73995d2e005'),
            'Another category title',
            'Another category description',
            'Some text',
            new Slug('service'),
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn($category);

        $this->categories->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug), $this->equalTo($directionId))
            ->willReturn($anotherCategory);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category with slug service is exists.');

        $this->handler->handle($command);
    }
    private function getCommand(): Command
    {
        return new Command(
            'fca9967e-2db1-45bf-afc3-bb121d622348',
            'Служба охраны труда',
            'Служба охраны труда - комплект документов',
            'Some text',
            'service',
            '3b30a1da-2ce1-49d8-a994-d0fb222ad827'
        );
    }
}