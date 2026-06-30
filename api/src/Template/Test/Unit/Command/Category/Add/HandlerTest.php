<?php

namespace App\Template\Test\Unit\Command\Category\Add;

use App\Template\Command\Direction\Category\Add\Command;
use App\Template\Command\Direction\Category\Add\Handler;
use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Category\CategoryRepository;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Direction\DirectionRepository;
use App\Template\Entity\Slug;
use App\Template\Test\Builder\CategoryBuilder;
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
    /** @var CategoryRepository&MockObject  */
    private CategoryRepository $categories;
    private Handler $handler;

    public function setUp(): void
    {
        $this->directions = $this->createMock(DirectionRepository::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->categories = $this->createMock(CategoryRepository::class);
        $this->handler = new Handler($this->directions, $this->categories, $this->flusher);
    }
    public function testDirectionNotFound(): void
    {
        $directionId = 'ebd10adf-e9e1-42c3-a0ae-5e14d2be4ff5';
        $command = $this->createCommand($directionId);
        $directionId = new DirectionId($command->directionId);

        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn(null);

        $this->categories->expects(self::never())->method('findById');
        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Direction not found.');
        $this->handler->handle($command);
    }

    public function testCategorySlugExist(): void
    {
        $directionId = 'ebd10adf-e9e1-42c3-a0ae-5e14d2be4ff5';
        $command = $this->createCommand($directionId);
        $slug = Slug::generate($command->title);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())
            ->withId($directionId)
            ->withTitle('Охрана труда')
            ->withDescription('Описание охрана труда')
            ->withText('Текст охрана труда')
            ->build();

        new Category(
            new CategoryId('80d53e36-49ad-48e7-b2c5-c4d5b8fa3de1'),
            'Обучение охраны труда',
            'Обучение охраны труда - комплект документов',
            'Some text',
            $slug->getValue(),
            $direction
        );

        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category with slug sluzba-ohrany-truda is exists.');
        $this->handler->handle($command);
    }
    public function testSuccess(): void
    {
        $directionId = 'ebd10adf-e9e1-42c3-a0ae-5e14d2be4ff5';
        $command = $this->createCommand($directionId);
        $slug = Slug::generate($command->title);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())
            ->withId($directionId)
            ->withTitle('Охрана труда')
            ->withDescription('Описание охрана труда')
            ->withText('Текст охрана труда')
            ->build();


        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($command->title, $direction->getCategories()[0]->getTitle());
        self::assertEquals($command->description, $direction->getCategories()[0]->getDescription());
        self::assertEquals($slug->getValue(), $direction->getCategories()[0]->getSlug());
        self::assertEquals($command->text, $direction->getCategories()[0]->getText());

    }
    public function testSuccessWithParentCategory(): void
    {
        $directionId = 'ebd10adf-e9e1-42c3-a0ae-5e14d2be4ff5';
        $parentCategoryId = '79e25e47-6259-475f-8240-b1e52ef20874';
        $command = $this->createCommand($directionId, $parentCategoryId);
        Slug::generate($command->title);

        $direction = (new DirectionBuilder())
            ->withId(new DirectionId($directionId))
            ->withTitle('Охрана труда')
            ->withDescription('Описание охрана труда')
            ->withText('Текст охрана труда')
            ->build();

        $parentCategory = (new CategoryBuilder())
            ->withCategoryId(new CategoryId($parentCategoryId))
            ->build($direction);

        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo(new DirectionId($directionId)))
            ->willReturn($direction);

        $this->categories->expects(self::once())
            ->method('findById')
            ->with($this->equalTo(new CategoryId($parentCategoryId)))
            ->willReturn($parentCategory);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        $createdCategories = $direction->getCategories();
        self::assertCount(2, $createdCategories);

        $newCategory = $createdCategories[1];
        self::assertNotNull($newCategory->getParent());
        self::assertEquals($parentCategoryId, $newCategory->getParent()->getId()->getValue());
    }
    public function testParentCategoryNotFound(): void
    {
        $directionId = 'ebd10adf-e9e1-42c3-a0ae-5e14d2be4ff5';
        $parentCategoryId = '79e25e47-6259-475f-8240-b1e52ef20874';
        $command = $this->createCommand($directionId, $parentCategoryId);
        Slug::generate($command->title);

        $direction = (new DirectionBuilder())
            ->withId(new DirectionId($directionId))
            ->withTitle('Охрана труда')
            ->withDescription('Описание охрана труда')
            ->withText('Текст охрана труда')
            ->build();

        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo(new DirectionId($directionId)))
            ->willReturn($direction);

        $this->categories->expects(self::once())
            ->method('findById')
            ->with($this->equalTo(new CategoryId($parentCategoryId)))
            ->willReturn(null);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Parent category not found.');

        $this->handler->handle($command);
    }
    private function createCommand(string $directionId, string $parentId = null): Command
    {
        return new Command(
            $directionId,
            'Служба охраны труда',
            'Описание службы охраны труда',
            'Текст службы охраны труда',
            $parentId
        );
    }
}