<?php

namespace App\Direction\Test\Unit\Command\Category\Add;

use App\Direction\Command\Direction\Category\Add\Command;
use App\Direction\Command\Direction\Category\Add\Handler;
use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Flusher;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    private DirectionRepository $directions;
    private Flusher $flusher;

    public function setUp(): void
    {
        $this->directions = $this->createMock(DirectionRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->handler = new Handler($this->directions, $this->flusher);
    }
    public function testDirectionNotFound(): void
    {
        $command = $this->createCommand();
        $directionId = new DirectionId($command->directionId);
        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn(null);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Direction not found.');
        $this->handler->handle($command);
    }

    public function testDuplicateCategory(): void
    {
        $command = $this->createCommand();
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())
            ->withId(new DirectionId('1f271404-e5af-4bb8-8416-81642e66fc6b'))
            ->withTitle('Охрана труда')
            ->withDescription('Описание охрана труда')
            ->withText('Текст охрана труда')
            ->withSlug(new Slug('safety'))
            ->build();

        $existingCategory = new Category(
            new CategoryId('80d53e36-49ad-48e7-b2c5-c4d5b8fa3de1'),
            'Обучение охраны труда',
            'Обучение охраны труда - комплект документов',
            'Some text',
            new Slug('service'),
            $direction
        );

        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category with slug service is exists.');
        $this->handler->handle($command);
    }
    public function testSuccess(): void
    {
        $command = $this->createCommand();
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())
            ->withId(new DirectionId($command->directionId))
            ->withTitle('Охрана труда')
            ->withDescription('Описание охрана труда')
            ->withText('Текст охрана труда')
            ->withSlug(new Slug('safety'))
            ->build();


        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($command->title, $direction->getCategories()[0]->getTitle());
        self::assertEquals($command->description, $direction->getCategories()[0]->getDescription());
        self::assertEquals($command->slug, $direction->getCategories()[0]->getSlug()->getValue());
        self::assertEquals($command->text, $direction->getCategories()[0]->getText());

    }


    private function createCommand(): Command
    {
        return new Command(
          'ebd10adf-e9e1-42c3-a0ae-5e14d2be4ff5',
            'Служба охраны труда',
            'Описание службы охраны труда',
            'Текст службы охраны труда',
            'service'
        );
    }
}