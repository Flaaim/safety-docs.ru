<?php

namespace App\Template\Test\Unit\Command\Direction\Delete;

use App\Template\Command\Direction\Delete\Command;
use App\Template\Command\Direction\Delete\Handler;
use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Direction\DirectionRepository;
use App\Template\Entity\Slug;
use App\Template\Test\Builder\DirectionBuilder;
use App\Flusher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    /** @var DirectionRepository&MockObject  */
    private DirectionRepository $directions;
    /** @var Flusher&MockObject  */
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
            ->build();

        new Category(
            CategoryId::generate(),
            $title = 'Служба охраны труда',
            'Category Description',
            'Category Text',
            Slug::generate($title),
            $direction
        );

        new Category(
            CategoryId::generate(),
            $title = 'Медосмотры',
            'Category Description',
            'Category Text',
            Slug::generate($title),
            $direction
        );

        return $direction;
    }
}