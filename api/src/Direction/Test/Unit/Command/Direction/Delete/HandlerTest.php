<?php

namespace App\Direction\Test\Unit\Command\Direction\Delete;

use App\Direction\Command\Direction\Delete\Command;
use App\Direction\Command\Direction\Delete\Handler;
use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Flusher;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    private DirectionRepository $directions;
    private Flusher $flusher;
    private Handler  $handler;
    public function setUp(): void
    {
       $this->directions = $this->createMock(DirectionRepository::class);
       $this->flusher = $this->createMock(Flusher::class);
       $this->handler = new Handler($this->directions, $this->flusher);
    }
    public function testSuccess(): void
    {
        $command = new Command('c3bb9705-432f-416b-8054-6a3642468fa5');

        $directionId = new DirectionId($command->directionId);
        $direction = (new DirectionBuilder())->build();
        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->directions->expects(self::once())->method('remove')
        ->with($this->equalTo($direction));

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);
    }
    public function testNotFound(): void
    {
        $command = new Command('c3bb9705-432f-416b-8054-6a3642468fa5');

        $directionId = new DirectionId($command->directionId);
        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn(null);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Direction not found.');
        $this->handler->handle($command);
    }

    public function testCantDelete(): void
    {
        $command = new Command('c3bb9705-432f-416b-8054-6a3642468fa5');

        $directionId = new DirectionId($command->directionId);

        $direction = $this->getDirection();
        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);


        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Direction cannot be deleted.');
        $this->handler->handle($command);
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