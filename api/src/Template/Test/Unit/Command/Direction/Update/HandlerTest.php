<?php

namespace App\Template\Test\Unit\Command\Direction\Update;

use App\Template\Command\Direction\Update\Command;
use App\Template\Command\Direction\Update\Handler;
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
    private Handler $handler;

    public function setUp(): void
    {
        $this->directions = $this->createMock(DirectionRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->handler = new Handler($this->directions, $this->flusher);
    }

    public function testDirectionNotFound(): void
    {
        $command = $this->createValidCommand();
        $directionId = $command->directionId;
        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn(null);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Direction not found.');

        $this->handler->handle($command);
    }
    public function testSuccessWithNewSlug(): void
    {
        $command = $this->createValidCommand();
        $directionId = new DirectionId($command->directionId);
        $slug = Slug::generate($command->title);

        $direction = (new DirectionBuilder())->build();

        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->directions->expects(self::once())
            ->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn(null);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($command->title, $direction->getTitle());
        self::assertEquals($command->description, $direction->getDescription());
        self::assertEquals($slug->getValue(), $direction->getSlug());
        self::assertEquals($command->text, $direction->getText());
    }



    public function testSuccessWithSameSlug(): void
    {
        $command = $this->createValidCommand();
        $directionId = new DirectionId($command->directionId);
        $slug = Slug::generate($command->title);

        $direction = (new DirectionBuilder())
            ->withId($directionId)
            ->withTitle('Старое название')
            ->withDescription('Старое описание')
            ->withText('Старый текст')
            ->build();


        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);


        $this->directions->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))->willReturn($direction);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($command->title, $direction->getTitle());
        self::assertEquals($command->description, $direction->getDescription());
        self::assertEquals($slug->getValue(), $direction->getSlug());
        self::assertEquals($command->text, $direction->getText());
    }

    public function testSlugAlreadyTakenByAnotherDirection(): void
    {
        $command = $this->createValidCommand();
        $directionId = new DirectionId($command->directionId);
        $slug = Slug::generate($command->title);
        $anotherDirectionId = new DirectionId('e019e716-3d33-47a9-8b1f-b6f62114b7ab');

        $direction = (new DirectionBuilder())
            ->withId($directionId)
            ->build();

        $anotherDirection = (new DirectionBuilder())
            ->withId($anotherDirectionId)
            ->build();

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->directions->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug))
            ->willReturn($anotherDirection);

        $this->flusher->expects(self::never())->method('flush');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Direction with this slug already exists.');

        $this->handler->handle($command);

    }

    private function createValidCommand(): Command
    {
        return new Command(
            '5764bba3-dd03-4fe9-b188-98e4c40ecb94',
            'Пожарная безопасность',
            'Описание пожарная безопасность',
            'Текст пожарная безопасность',
        );
    }
}