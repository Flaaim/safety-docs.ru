<?php

namespace App\Direction\Test\Unit\Command\Direction\Add;

use App\Direction\Command\Direction\Add\Command;
use App\Direction\Command\Direction\Add\Handler;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Flusher;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    private DirectionRepository $directions;
    private Flusher $flusher;

    public function setUp(): void
    {
        $this->directions = $this->createMock(DirectionRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
    }
    public function testExists(): void
    {
        $command = new Command(
            'Охрана труда',
            'Описание охрана труда',
            'Текст охрана труда',
            $slug = 'safety'
        );
        $handler = new Handler($this->directions, $this->flusher);

        $this->directions->expects(self::once())->method('findBySlug')
            ->with(self::equalTo(new Slug($slug)))
            ->willReturn($direction = (new DirectionBuilder())->build());

        $this->directions->expects(self::never())->method('add');
        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage("Direction with slug ".$command->slug." is exists");
        $handler->handle($command);
    }

    public function testSuccess(): void
    {
        $command = new Command(
           'Охрана труда',
            'Описание охрана труда',
            'Текст охрана труда',
           'safety'
        );

        $handler = new Handler($this->directions, $this->flusher);

        $this->directions->expects(self::once())->method('findBySlug')
            ->with(self::equalTo(new Slug($command->slug)))
            ->willReturn(null);

        $this->directions->expects(self::once())->method('add')->with(
            self::callback(static function ($direction) use ($command) {
                self::assertEquals($command->title, $direction->getTitle());
                self::assertEquals($command->description, $direction->getDescription());
                self::assertEquals($command->text, $direction->getText());
                self::assertEquals($command->slug, $direction->getSlug()->getValue());
                return true;
            })
        );

        $this->flusher->expects(self::once())->method('flush');

        $handler->handle($command);
    }
}