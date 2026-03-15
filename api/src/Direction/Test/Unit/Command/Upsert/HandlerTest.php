<?php

namespace App\Direction\Test\Unit\Command\Upsert;

use App\Direction\Command\Direction\Upsert\Command;
use App\Direction\Command\Direction\Upsert\Handler;
use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Flusher;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    public function testAdd(): void
    {
        $command = new Command(
          '2a7a593a-ee23-4a73-bb07-b372438fb269',
          'title',
          'description',
          'text',
            'slug',
        );
        $directions = $this->createMock(DirectionRepository::class);
        $flusher = $this->createMock(Flusher::class);


        $handler = new Handler($directions, $flusher);
        $directionId = new DirectionId($command->directionId);
        $directions->expects(self::once())->method('findById')
            ->with(self::equalTo($directionId))
            ->willReturn(null);

        $slug = new Slug($command->slug);
        $directions->expects(self::once())->method('existsBySlug')
            ->with(self::equalTo($slug))
            ->willReturn(false);

        $directions->expects(self::once())->method('add')->with(
            $this->isInstanceOf(Direction::class),
        );
        $flusher->expects(self::once())->method('flush');

        $handler->handle($command);
    }

    public function testAddSlugIsExists(): void
    {
        $command = new Command(
            '2a7a593a-ee23-4a73-bb07-b372438fb269',
            'title',
            'description',
            'text',
            'slug',
        );
        $directions = $this->createMock(DirectionRepository::class);
        $flusher = $this->createMock(Flusher::class);


        $handler = new Handler($directions, $flusher);
        $directionId = new DirectionId($command->directionId);
        $directions->expects(self::once())->method('findById')
            ->with(self::equalTo($directionId))
            ->willReturn(null);

        $slug = new Slug($command->slug);
        $directions->expects(self::once())->method('existsBySlug')
            ->with(self::equalTo($slug))
            ->willReturn(true);

        $directions->expects(self::never())->method('add');
        $flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Direction slug already exists.');
        $handler->handle($command);
    }


    public function testUpdate(): void
    {
        $command = new Command(
            '2a7a593a-ee23-4a73-bb07-b372438fb269',
            'title',
            'description',
            'text',
            'slug',
        );
        $directions = $this->createMock(DirectionRepository::class);
        $flusher = $this->createMock(Flusher::class);

        $directionId = new DirectionId($command->directionId);
        $slug = new Slug('another-slug');

        $directions->expects(self::once())->method('findById')
            ->with($directionId)
            ->willReturn($direction = (new DirectionBuilder())->withSlug($slug)->build());

        $directions->expects(self::once())->method('existsBySlug')->willReturn(false);

        $flusher->expects(self::once())->method('flush');
        $handler = new Handler($directions, $flusher);

        $handler->handle($command);

        self::assertEquals('title', $direction->getTitle());
        self::assertEquals('description', $direction->getDescription());
        self::assertEquals('text', $direction->getText());
        self::assertEquals('slug', $direction->getSlug()->getValue());
    }

    public function testUpdateSlugIsExists(): void
    {
        $command = new Command(
            '2a7a593a-ee23-4a73-bb07-b372438fb269',
            'title',
            'description',
            'text',
            'slug',
        );
        $directions = $this->createMock(DirectionRepository::class);
        $flusher = $this->createMock(Flusher::class);

        $directionId = new DirectionId($command->directionId);
        $slug = new Slug('another-slug');

        $directions->expects(self::once())->method('findById')
            ->with($directionId)
            ->willReturn((new DirectionBuilder())->withSlug($slug)->build());

        $directions->expects(self::once())->method('existsBySlug')->willReturn(true);

        $handler = new Handler($directions, $flusher);

        $flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('New slug is already taken by another direction.');

        $handler->handle($command);
    }
    public function testUpdateWithoutSlugChange(): void
    {
        $command = new Command(
            '2a7a593a-ee23-4a73-bb07-b372438fb269',
            'new title',
            'description',
            'text',
            'slug',
        );

        $directions = $this->createMock(DirectionRepository::class);
        $flusher = $this->createMock(Flusher::class);

        $directionId = new DirectionId($command->directionId);
        $slug = new Slug('slug');

        $directions->expects(self::once())->method('findById')->with($directionId)->willReturn($direction = (new DirectionBuilder())->withSlug($slug)->build());

        $directions->expects(self::never())->method('existsBySlug');

        $flusher->expects(self::once())->method('flush');

        $handler = new Handler($directions, $flusher);

        $handler->handle($command);

        self::assertEquals('new title', $direction->getTitle());
        self::assertEquals('description', $direction->getDescription());
        self::assertEquals('text', $direction->getText());
        self::assertEquals('slug', $direction->getSlug()->getValue());

    }
}