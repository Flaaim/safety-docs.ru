<?php

namespace App\Direction\Test\Unit\Command\Category\GetBySlug;

use App\Direction\Command\Direction\Category\GetBySlug\Command;
use App\Direction\Command\Direction\Category\GetBySlug\Handler;
use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    private CategoryRepository $categories;
    private Handler $handler;
    public function setUp(): void
    {
        $this->categories = $this->createMock(CategoryRepository::class);
        $this->handler = new Handler($this->categories);
    }

    public function testNotFound(): void
    {
        $command = new Command('service', 'e42b8e4f-0ac3-4cca-984d-4f1dc983e970');

        $this->categories->expects($this->once())->method('findBySlug')->willReturn(null);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category not found.');

        $this->handler->handle($command);
    }

    public function testSuccess(): void
    {
        $command = new Command('service', 'e42b8e4f-0ac3-4cca-984d-4f1dc983e970');

        $category = $this->getCategory();

        $this->categories->expects($this->once())->method('findBySlug')
            ->with(
                $this->equalTo(new Slug($command->slug)),
                $this->equalTo(new DirectionId($command->directionId))
            )
            ->willReturn($category);

        $categoryDTO = $this->handler->handle($command);

        self::assertEquals('Служба охраны труда', $categoryDTO->title);
        self::assertEquals('Служба охраны труда - комплект документов', $categoryDTO->description);
        self::assertEquals('Some text', $categoryDTO->text);
        self::assertEquals('service', $categoryDTO->slug);
        self::assertEquals('e42b8e4f-0ac3-4cca-984d-4f1dc983e970', $categoryDTO->directionId);
        self::assertEquals('8aa8f453-b19b-4b53-915b-1f04c83a9aee', $categoryDTO->id);
        self::assertEquals('Охрана труда', $categoryDTO->directionTitle);
    }

    private function getCategory(): Category
    {
        $direction = (new DirectionBuilder())
            ->withId(new DirectionId('e42b8e4f-0ac3-4cca-984d-4f1dc983e970'))
            ->withTitle('Охрана труда')
            ->withDescription('Охрана труда описание')
            ->withText('Охрана труда текст')
            ->withSlug(new Slug('safety'))
            ->build();

        return new Category(
            new CategoryId('8aa8f453-b19b-4b53-915b-1f04c83a9aee'),
            'Служба охраны труда',
            'Служба охраны труда - комплект документов',
            'Some text',
            new Slug('service'),
            $direction
        );
    }
}