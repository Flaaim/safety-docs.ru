<?php

namespace App\Direction\Test\Unit\Command\Direction\AddCategory;

use App\Direction\Command\Direction\AddCategory\Command;
use App\Direction\Command\Direction\AddCategory\Handler;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Flusher;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    public function testSuccess(): void
    {
        $command = new Command(
            '1fb44857-d64e-432d-83b5-abe48a42d6ae',
            '74083e3c-35ad-4448-a1aa-702b9d803922',
            'Служба охраны труда - образцы документов',
            'Собраны комплекты образцов документов по организации на предприятии службы охраны труда',
            'Оцените численность штата. Если в организации более 50 человек — создайте службу охраны труда',
            'service'
        );

        $directions = $this->createMock(DirectionRepository::class);
        $flusher = $this->createMock(Flusher::class);

        $handler = new Handler($directions, $flusher);

        $directionId = new DirectionId($command->directionId);
        $directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction = (new DirectionBuilder())->build());

        $flusher->expects(self::once())->method('flush');

        $handler->handle($command);

        self::assertCount(1, $direction->getCategories());
    }

    public function testNotFound(): void
    {
        $command = new Command(
            '1fb44857-d64e-432d-83b5-abe48a42d6ae',
            '74083e3c-35ad-4448-a1aa-702b9d803922',
            'Служба охраны труда - образцы документов',
            'Собраны комплекты образцов документов по организации на предприятии службы охраны труда',
            'Оцените численность штата. Если в организации более 50 человек — создайте службу охраны труда',
            'service'
        );

        $directions = $this->createMock(DirectionRepository::class);
        $flusher = $this->createMock(Flusher::class);



        $directionId = new DirectionId($command->directionId);
        $directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn(null);


        $handler = new Handler($directions, $flusher);

        $flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Direction not found.');

        $handler->handle($command);
    }
}