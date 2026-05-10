<?php

namespace App\Direction\Test\Unit\Command\Category\Update;

use App\Direction\Command\Direction\Category\Update\Command;
use App\Direction\Command\Direction\Category\Update\Handler;
use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Flusher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    /** @var CategoryRepository&MockObject  */
    private CategoryRepository $categories;
    /** @var DirectionRepository&MockObject  */
    private DirectionRepository $directions;
    private Handler $handler;
    /** @var Flusher&MockObject  */
    private Flusher $flusher;
    public function setUp(): void
    {
        $this->categories = $this->createMock(CategoryRepository::class);
        $this->directions = $this->createMock(DirectionRepository::class);

        $this->flusher = $this->createMock(Flusher::class);
        $this->handler = new Handler($this->categories, $this->directions, $this->flusher);
    }

    public function testDirectionNotFound(): void
    {
        $command = $this->getCommand(
            '79e25e47-6259-475f-8240-b1e52ef20874',
            'f29f16f3-cbf1-415a-9ec6-760930ba8780'
        );
        $directionId = new DirectionId($command->directionId);

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn(null);

        $this->categories->expects(self::never())->method('findById');
        $this->categories->expects(self::never())->method('findBySlug');
        $this->categories->expects(self::never())->method('findById');
        $this->flusher->expects(self::never())->method('flush');

        self::expectExceptionMessage('Direction not found.');
        self::expectException(\DomainException::class);
        $this->handler->handle($command);
    }

    public function testCategoryNotFound(): void
    {
        $command = $this->getCommand(
            '79e25e47-6259-475f-8240-b1e52ef20874',
            'f29f16f3-cbf1-415a-9ec6-760930ba8780'
        );
        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->build();

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn(null);

        $this->categories->expects(self::never())->method('findBySlug');
        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category not found.');

        $this->handler->handle($command);
    }
    public function testParentCategoryNotFound(): void
    {
        $parentCategoryId =  new CategoryId('44f6861d-952a-4c6a-acf8-77631d5cae23');
        $command = $this->getCommand(
            '79e25e47-6259-475f-8240-b1e52ef20874',
            $parentCategoryId->getValue()
        );
        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->withId($directionId)->build();

        $slug = Slug::generate($title = 'Инструкция о мерах пожбезопасности');

        $childCategory = new Category(
            $categoryId,
            $title,
            'Описание инструкции',
            'Some text',
            $slug,
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->exactly(2))->method('findById')
            ->willReturnCallback(function (CategoryId $id) use($childCategory) {
                if ($id->getValue() === $childCategory->getId()->getValue()) {
                    return $childCategory;
                }
                return null;
            });

        $this->flusher->expects(self::never())->method('flush');
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Parent category not found.');
        $this->handler->handle($command);
    }
    public function testCannotBeOwnParent(): void
    {
        $parentCategoryId =  new CategoryId('79e25e47-6259-475f-8240-b1e52ef20874');
        $command = $this->getCommand(
            '79e25e47-6259-475f-8240-b1e52ef20874',
            $parentCategoryId->getValue()
        );
        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->withId($directionId)->build();

        $slug = Slug::generate($title = 'Инструкция о мерах пожбезопасности');
        $category =  new Category(
            $categoryId,
            $title,
            'Описание инструкции',
            'Some text',
            $slug,
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->exactly(2))->method('findById')
            ->willReturnCallback(function (CategoryId $id) use($category) {
                if ($id->getValue() === $category->getId()->getValue()) {
                    return $category;
                }
                return null;
            });
        $this->categories->expects(self::once())->method('findBySlug')->willReturn(null);
        $this->flusher->expects(self::never())->method('flush');
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('A category cannot be its own parent.');
        $this->handler->handle($command);
    }
    public function testParentFromDifferentDirection(): void
    {
        $parentCategoryId = new CategoryId('44f6861d-952a-4c6a-acf8-77631d5cae23');
        $command = $this->getCommand(
            '79e25e47-6259-475f-8240-b1e52ef20874',
            $parentCategoryId->getValue()
        );

        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);
        $anotherDirectionId = new DirectionId('b6725986-789a-4341-9f57-2e779cd686d1');

        $direction = (new DirectionBuilder())->withId($directionId)->build();
        $anotherDirection = (new DirectionBuilder())->withId($anotherDirectionId)->build();

        $parentSlug = Slug::generate($title = 'Пожарная безопасность');
        $parentCategory =  new Category(
            $parentCategoryId,
            $title,
            'Комплект документов',
            'Some text',
            $parentSlug,
            $anotherDirection
        );

        $childSlug = Slug::generate($title = 'Инструкция о мерах пожбезопасности');
        $childCategory = new Category(
            $categoryId,
            $title,
            'Описание инструкции',
            'Some text',
            $childSlug,
            $direction
        );
        $this->directions->expects(self::once())
            ->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->exactly(2))->method('findById')
            ->willReturnCallback(function (CategoryId $id) use($parentCategory, $childCategory) {
                if ($id->getValue() === $childCategory->getId()->getValue()) {
                    return $childCategory;
                }
                if($id->getValue() === $parentCategory->getId()->getValue()){
                    return $parentCategory;
                }
                return null;
            });

        $this->categories->expects(self::once())->method('findBySlug')
            ->with($this->equalTo(Slug::generate($command->title)), $this->equalTo($directionId))
            ->willReturn(null);

        $this->flusher->expects(self::never())->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Child category cannot be from different direction.');
        $this->handler->handle($command);
    }
    public function testSuccessParentWithNewSlug(): void
    {
        $command = $this->getCommand(
            '79e25e47-6259-475f-8240-b1e52ef20874'
        );
        $slug = Slug::generate($command->title);
        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->withId($directionId)->build();

        $parentCategory =  new Category(
            new CategoryId($command->categoryId),
            $title = 'Пожарная безопасность',
            'Комплект документов',
            'Some text',
            Slug::generate($title),
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn($parentCategory);

        $this->categories->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug), $this->equalTo($directionId))
            ->willReturn(null);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($command->title, $parentCategory->getTitle());
        self::assertEquals($command->description, $parentCategory->getDescription());
        self::assertEquals($slug->getValue(), $parentCategory->getSlug()->getValue());
        self::assertEquals($command->text, $parentCategory->getText());
    }

    public function testSuccessChildrenWithNewSlug(): void
    {
        $parentCategoryId =  new CategoryId('44f6861d-952a-4c6a-acf8-77631d5cae23');
        $command = $this->getCommand(
            '79e25e47-6259-475f-8240-b1e52ef20874',
            $parentCategoryId->getValue()
        );
        $slug = Slug::generate($command->title);

        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->withId($directionId)->build();

        $parentCategory =  new Category(
            $parentCategoryId,
            $title = 'Пожарная безопасность',
            'Комплект документов',
            'Some text',
            Slug::generate($title),
            $direction
        );
        $childCategory =  new Category(
            $categoryId,
            $title = 'Инструкция о мерах пожбезопасности',
            'Описание инструкции',
            'Some text',
            Slug::generate($title),
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->exactly(2))->method('findById')
            ->willReturnCallback(function (CategoryId $id) use($parentCategory, $childCategory) {
                if ($id->getValue() === $childCategory->getId()->getValue()) {
                    return $childCategory;
                }
                if($id->getValue() === $parentCategory->getId()->getValue()){
                    return $parentCategory;
                }
                return null;
            });

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($command->title, $childCategory->getTitle());
        self::assertEquals($command->description, $childCategory->getDescription());
        self::assertEquals($slug->getValue(), $childCategory->getSlug()->getValue());
        self::assertEquals($command->text, $childCategory->getText());
        self::assertEquals($parentCategoryId, $childCategory->getParent()->getId());
    }

    public function testSuccessWithSameSlug(): void
    {
        $command = $this->getCommand('79e25e47-6259-475f-8240-b1e52ef20874');
        $slug = Slug::generate($command->title);
        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->withId($directionId)->build();

        $category =  new Category(
            new CategoryId($command->categoryId),
            $title = 'Пожарная безопасность',
            'Комплект документов',
            'Some text',
            Slug::generate($title),
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn($category);

        $this->categories->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug), $this->equalTo($directionId))
            ->willReturn($category);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals($command->title, $category->getTitle());
        self::assertEquals($command->description, $category->getDescription());
        self::assertEquals($slug->getValue(), $category->getSlug()->getValue());
        self::assertEquals($command->text, $category->getText());
    }

    public function testAlreadyTakenAnotherCategory(): void
    {
        $command = $this->getCommand('79e25e47-6259-475f-8240-b1e52ef20874');
        $slug = Slug::generate($command->title);
        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())->withId($directionId)->build();

        $category =  new Category(
            new CategoryId($command->categoryId),
            $title = 'Пожарная безопасность',
            'Комплект документов',
            'Some text',
            Slug::generate($title),
            $direction
        );

        $anotherCategory =  new Category(
            new CategoryId('7f2cf3f7-9f47-4d04-ae5e-d73995d2e005'),
            $title = 'Another category title',
            'Another category description',
            'Some text',
            Slug::generate($title),
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($direction);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn($category);

        $this->categories->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug), $this->equalTo($directionId))
            ->willReturn($anotherCategory);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category with slug sluzba-ohrany-truda is exists.');

        $this->handler->handle($command);
    }

    public function testSuccessWithNewDirection(): void
    {
        $command = $this->getCommand('79e25e47-6259-475f-8240-b1e52ef20874');
        $slug = Slug::generate($command->title);
        $categoryId = new CategoryId($command->categoryId);
        $directionId = new DirectionId($command->directionId);

        $direction = (new DirectionBuilder())
            ->withTitle('Охрана труда')
            ->withId(new DirectionId('658c6a52-09df-4ebb-acc4-b83c2b6abe48'))
            ->build();

        $newDirection = (new DirectionBuilder())
            ->withTitle('Пожарная безопасность')
            ->withId(new DirectionId('3b30a1da-2ce1-49d8-a994-d0fb222ad827'))->build();

        $category =  new Category(
            new CategoryId($command->categoryId),
            $title = 'Пожарная безопасность',
            'Комплект документов',
            'Some text',
            Slug::generate($title),
            $direction
        );

        $this->directions->expects(self::once())->method('findById')
            ->with($this->equalTo($directionId))
            ->willReturn($newDirection);

        $this->categories->expects($this->once())->method('findById')
            ->with($this->equalTo($categoryId))
            ->willReturn($category);

        $this->categories->expects(self::once())->method('findBySlug')
            ->with($this->equalTo($slug), $this->equalTo($directionId))
            ->willReturn(null);

        $this->flusher->expects(self::once())->method('flush');

        $this->handler->handle($command);

        self::assertEquals('Пожарная безопасность', $category->getDirection()->getTitle());
    }


    private function getCommand(string $categoryId, ?string $parentId = null): Command
    {
        return new Command(
            $categoryId,
            'Служба охраны труда',
            'Служба охраны труда - комплект документов',
            'Some text',
            '3b30a1da-2ce1-49d8-a994-d0fb222ad827',
            $parentId
        );
    }
}