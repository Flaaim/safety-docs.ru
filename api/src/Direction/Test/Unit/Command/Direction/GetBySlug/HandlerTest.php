<?php

namespace App\Direction\Test\Unit\Command\Direction\GetBySlug;

use App\Direction\Command\Direction\GetBySlug\Command;
use App\Direction\Command\Direction\GetBySlug\Handler;
use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\DTO\CategoryDTOMapper;
use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Direction\DTO\DirectionDTO;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    private DirectionRepository $directions;
    private CategoryDTOMapper $categoryDTOMapper;
    public function setUp(): void
    {
        $this->directions = $this->createMock(DirectionRepository::class);
        $this->categoryDTOMapper = new CategoryDTOMapper();
        $this->handler = new Handler($this->directions, $this->categoryDTOMapper);
    }

    public function testNotFoundDirection(): void
    {
        $command = new Command('safety');
        $slug = new Slug($command->slug);
        $this->directions->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn(null);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Direction not found.');

        $this->handler->handle($command);
    }

    public function testSuccess(): void
    {
        $command = new Command('safety');
        $slug = new Slug($command->slug);

        $direction = $this->getDirection();

        $this->directions->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($direction);

        $directionDTO = $this->handler->handle($command);

        self::assertEquals('Охрана труда', $directionDTO->title);
        self::assertEquals('Охрана труда описание', $directionDTO->description);
        self::assertEquals('Охрана труда текст', $directionDTO->text);
        self::assertEquals('safety', $directionDTO->slug);

        self::assertCount(2, $directionDTO->categories);

    }



    private function getDirection(): Direction
    {
        $direction = (new DirectionBuilder())
            ->withTitle('Охрана труда')
            ->withDescription('Охрана труда описание')
            ->withText('Охрана труда текст')
            ->withSlug(new Slug('safety'))
            ->build();

        new Category(
            CategoryId::generate(),
            'Служба охраны труда',
            'Category Description',
            'Category Text',
            new Slug('service'),
            $direction
        );

        new Category(
            CategoryId::generate(),
            'Медосмотры',
            'Category Description',
            'Category Text',
            new Slug('medical'),
            $direction
        );

        return $direction;
    }
}