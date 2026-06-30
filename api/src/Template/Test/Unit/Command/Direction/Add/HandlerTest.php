<?php

namespace App\Template\Test\Unit\Command\Direction\Add;

use App\Template\Command\Direction\Add\Command;
use App\Template\Command\Direction\Add\Handler;
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
        );
        $slug = Slug::generate($command->title);
        $handler = new Handler($this->directions, $this->flusher);

        $this->directions->expects(self::once())->method('findBySlug')
            ->with(self::equalTo($slug))
            ->willReturn((new DirectionBuilder())->build());

        $this->directions->expects(self::never())->method('add');
        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage("Direction with slug ".$slug->getValue()." is exists");
        $handler->handle($command);
    }

    public function testSuccess(): void
    {
        $command = new Command(
           'Охрана труда',
            'Описание охрана труда',
            'Текст охрана труда',
        );
        $slug = Slug::generate($command->title);
        $handler = new Handler($this->directions, $this->flusher);

        $this->directions->expects(self::once())->method('findBySlug')
            ->with(self::equalTo($slug))
            ->willReturn(null);

        $this->directions->expects(self::once())->method('add')->with(
            self::callback(static function ($direction) use ($command, $slug) {
                self::assertEquals($command->title, $direction->getTitle());
                self::assertEquals($command->description, $direction->getDescription());
                self::assertEquals($command->text, $direction->getText());
                self::assertEquals($slug->getValue(), $direction->getSlug()->getValue());
                return true;
            })
        );

        $this->flusher->expects(self::once())->method('flush');

        $handler->handle($command);
    }
}