<?php

namespace App\Direction\Test\Unit\Command\Direction\Update;

use App\Direction\Command\Direction\Update\Command;
use App\Direction\Command\Direction\Update\Handler;
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
        $command = $this->createValidCommand('new-slug');
        $directionId = new DirectionId($command->directionId);
        $slug = new Slug($command->slug);
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
        self::assertEquals($command->slug, $direction->getSlug()->getValue());
        self::assertEquals($command->text, $direction->getText());
    }



    public function testSuccessWithSameSlug(): void
    {
        $command = $this->createValidCommand('fire');
        $directionId = new DirectionId($command->directionId);
        $slug = new Slug($command->slug);

        $direction = (new DirectionBuilder())
            ->withId($directionId)
            ->withTitle('Старое название')
            ->withDescription('Старое описание')
            ->withText('Старый текст')
            ->withSlug($slug)
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
        self::assertEquals($command->slug, $direction->getSlug()->getValue());
        self::assertEquals($command->text, $direction->getText());
    }

    public function testSlugAlreadyTakenByAnotherDirection(): void
    {
        $command = $this->createValidCommand('new-slug');
        $directionId = new DirectionId($command->directionId);
        $slug = new Slug($command->slug);
        $anotherDirectionId = new DirectionId('e019e716-3d33-47a9-8b1f-b6f62114b7ab');

        $direction = (new DirectionBuilder())
            ->withId($directionId)
            ->withSlug(new Slug('old-slug'))
            ->build();

        $anotherDirection = (new DirectionBuilder())
            ->withId($anotherDirectionId)
            ->withSlug($slug)
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

    private function createValidCommand(string $slug = 'fire'): Command
    {
        return new Command(
            '5764bba3-dd03-4fe9-b188-98e4c40ecb94',
            'Пожарная безопасность',
            'Описание пожарная безопасность',
            'Текст пожарная безопасность',
            $slug
        );
    }
}