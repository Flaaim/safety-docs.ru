<?php

namespace App\Template\Test\Unit\Entity\Category;

use App\Shared\Domain\ValueObject\Currency;
use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Document\Amount;
use App\Template\Entity\Document\Document;
use App\Template\Entity\Document\DocumentId;
use App\Template\Entity\Document\Filename;
use App\Template\Entity\Slug;
use App\Template\Test\Builder\CategoryBuilder;
use App\Template\Test\Builder\DirectionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CategoryTest extends TestCase
{
    public function testCreateCategory(): void
    {
        $direction = (new DirectionBuilder())->build();
        $category = (new CategoryBuilder())
            ->withTitle('Положения по охране труда')
            ->withDescription('Положения по охране труда - описание документов')
            ->build($direction);

        self::assertEquals('Положения по охране труда', $category->getTitle());
        self::assertEquals('Положения по охране труда - описание документов', $category->getDescription());

        self::assertNull($category->getParent());
        self::assertEmpty($category->getChildren());
        self::assertEmpty($category->getDocuments());
    }
    public function testCreateCategoryWithParent(): void
    {
        $direction = (new DirectionBuilder())->build();
        $parentCategory = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->build($direction);

        $childCategory = new Category(
            CategoryId::generate(),
            $title = 'Title',
            'Description',
            'Text',
            Slug::generate($title)->getValue(),
            $direction,
            $parentCategory
        );

        self::assertTrue($childCategory->isChild());
    }
    public function testCategoryTheOwnParent(): void
    {
        $direction = (new DirectionBuilder())->build();
        $parent = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->build($direction);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('A category cannot be its own parent.');

        new Category(
            $parent->getId(),
            $title = 'Title',
            'Description',
            'Text',
            Slug::generate($title)->getValue(),
            $direction,
            $parent
        );
    }
    public function testCategoryDifferentDirection(): void
    {
        $safetyDirection = (new DirectionBuilder())
            ->withId(new DirectionId('9300fdba-c736-4060-9206-4422bc652c08'))
            ->withTitle('Охрана труда')
            ->build();

        $fireDirection = (new DirectionBuilder())
            ->withId(new DirectionId('ab00c25c-5cf8-4ed0-b7eb-54f2cc8541a9'))
            ->withTitle('Пожарная безопасность')
            ->build();

        $parentCategory = new Category(
            CategoryId::generate(),
            $title = 'Parent category',
            'Parent description category',
            'Text',
            Slug::generate($title)->getValue(),
            $safetyDirection,
        );

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Child category cannot be from different direction.');

        new Category(
            CategoryId::generate(),
            $title = 'Child category',
            'Child description category',
            'Text',
            Slug::generate($title)->getValue(),
            $fireDirection,
            $parentCategory
        );

    }
    public function testRecurseUpdateChildren(): void
    {
        $safetyDirection = (new DirectionBuilder())
            ->withId(new DirectionId('9300fdba-c736-4060-9206-4422bc652c08'))
            ->withTitle('Охрана труда')
            ->build();

        $fireDirection = (new DirectionBuilder())
            ->withId(new DirectionId('ab00c25c-5cf8-4ed0-b7eb-54f2cc8541a9'))
            ->withTitle('Пожарная безопасность')
            ->build();

        $parentCategory = (new CategoryBuilder())
            ->withSlug(new Slug('parent'))
            ->build($safetyDirection);

        $childCategory = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(new Slug('child'))
            ->withParent($parentCategory)
            ->build($safetyDirection);

        $parentCategory->updateDirection($fireDirection);

        self::assertTrue($parentCategory->getDirection()->getId()->equals($fireDirection->getId()));
        self::assertTrue($childCategory->getDirection()->getId()->equals($fireDirection->getId()));
    }
    public function testAddChild(): void
    {
        $safetyDirection = $this->getDirection('9300fdba-c736-4060-9206-4422bc652c08', 'Safety');
        $parent = (new CategoryBuilder())->build($safetyDirection);

        $child1 = $this->getCategory($safetyDirection, 'parentCategory2');
        $child2 = $this->getCategory($safetyDirection, 'parentCategory3');

        $parent->addChild($child1);
        $parent->addChild($child2);

        self::assertCount(2, $parent->getChildren());
        self::assertSame($parent, $child1->getParent());
        self::assertSame($parent, $child2->getParent());

        self::assertTrue($child1->isChild());
        self::assertTrue($child2->isChild());
    }
    public function testAssignChildrenAlready(): void
    {
        $safetyDirection = $this->getDirection('9300fdba-c736-4060-9206-4422bc652c08', 'Safety');
        $categoryId1 = new CategoryId('727d77c0-fef1-443a-9487-60d5a61404f8');

        $child1 = (new CategoryBuilder())->withSlug(Slug::generate('child'))->withCategoryId($categoryId1)->build($safetyDirection);

        $parentCategory1 = (new CategoryBuilder())
            ->withChildren([$child1])
            ->build($safetyDirection);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('A category child already assigned.');
        $parentCategory1->addChild($child1);
    }
    public function testCanBeDeleted(): void
    {
        $direction = $this->getDirection();
        $category = $this->getCategory($direction);

        self::assertTrue($category->canBeDeleted());
    }

    public function testCanNotBeDeleted(): void
    {
        $direction = $this->getDirection();
        $category = (new CategoryBuilder())
            ->withChildren([$this->createMock(Category::class)])->build($direction);

        self::assertFalse($category->canBeDeleted());
    }
    public function testRemoveChild(): void
    {
        $direction = $this->getDirection();
        $parentCategory = $this->getCategory($direction, 'parent');
        $childCategory = $this->getCategory($direction, 'children', $parentCategory);

        $parentCategory->removeChild($childCategory);
        self::assertNull($childCategory->getParent());
    }
    public function testRelease(): void
    {
        $direction = $this->getDirection();
        $parentCategory = $this->getCategory($direction, 'parent');
        $childCategory = (new CategoryBuilder())
            ->withParent($parentCategory)
            ->build($direction);


        $childCategory->release();

        self::assertNull($childCategory->getParent());

    }
    private function getCategory(Direction $direction, string $slug = 'service', ?Category $parent = null): Category
    {
        return new Category(
            CategoryId::generate(),
            'Служба охраны труда',
            'Служба охраны труда, комплект документов',
            'some text',
            Slug::generate($slug)->getValue(),
            $direction,
            $parent
        );
    }

    private function getDirection(string $uuid = 'a393dded-51c5-4049-91ff-414b37ddf917', string $title = 'Охрана труда'): Direction
    {
        return (new DirectionBuilder())
            ->withId(new DirectionId($uuid))
            ->withTitle($title)
            ->build();
    }

    public function testAddDocumentSuccess(): void
    {
        $direction = (new DirectionBuilder())->withId(DirectionId::generate())->build();
        $category = (new CategoryBuilder())
            ->build($direction);

        new Document(
            DocumentId::generate(),
            'Инструкция по охране труда при работе на высоте',
            new Amount(200.00, new Currency('RUB')),
            new Filename(Uuid::uuid4()->toString(). '.docx'),
            'instructions',
            $category,
        );

        self::assertCount(1, $category->getDocuments());
    }

    public function testAddDocumentOnCategoryWithChild(): void
    {
        $direction = (new DirectionBuilder())->withId(DirectionId::generate())->build();
        $parentCategory = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('parent'))
            ->build($direction);

        (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('child'))
            ->withParent($parentCategory)
            ->build($direction);

        self::assertCount(1, $parentCategory->getChildren());

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Cannot add a document, because the current category contains subcategories.');
        new Document(
            DocumentId::generate(),
            'Инструкция по охране труда при работе на высоте',
            new Amount(200.00, new Currency('RUB')),
            new Filename(Uuid::uuid4()->toString(). '.docx'),
            'instructions',
            $parentCategory,
        );
    }

    public function testAddDocumentAlreadyAdded(): void
    {
        $direction = (new DirectionBuilder())->withId(DirectionId::generate())->build();

        $category = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->build($direction);

        new Document(
            new DocumentId('60fb976a-a30b-4dcd-a2b8-98a39fe17ebc'),
            'Инструкция по охране труда при работе на высоте',
            new Amount(200.00, new Currency('RUB')),
            new Filename(Uuid::uuid4()->toString(). '.docx'),
            'instructions',
            $category,
        );

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('A document already added in the current category.');
        new Document(
            new DocumentId('60fb976a-a30b-4dcd-a2b8-98a39fe17ebc'),
            'Инструкция по охране труда при работе на высоте',
            new Amount(200.00, new Currency('RUB')),
            new Filename(Uuid::uuid4()->toString(). '.docx'),
            'instructions',
            $category,
        );
    }
    public function testMakeRootAlready(): void
    {
        $direction = (new DirectionBuilder())->withId(DirectionId::generate())->build();
        $category = (new CategoryBuilder())
            ->build($direction);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('A category is already a root.');
        $category->makeRoot();
    }

    public function testMakeRootSuccess(): void
    {
        $direction = (new DirectionBuilder())->withId(DirectionId::generate())->build();
        $parent = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('parent'))
            ->build($direction);

        $child = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('child'))
            ->withParent($parent)
            ->build($direction);

        $child->makeRoot();

        self::assertNull($child->getParent());
        self::assertCount(0, $parent->getChildren());
    }

    public function testChangeParentSuccess(): void
    {
        $direction = (new DirectionBuilder())->withId(DirectionId::generate())->build();
        $oldParent = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('old-parent'))
            ->build($direction);

        $newParent = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('new-parent'))
            ->build($direction);

        $child = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('child'))
            ->withParent($oldParent)
            ->build($direction);

        $child->changeParent($newParent);

        self::assertEquals($newParent, $child->getParent());
        self::assertCount(0, $oldParent->getChildren());
        self::assertCount(1, $newParent->getChildren());
    }

    public function testChangeParentOwnParent(): void
    {
        $direction = (new DirectionBuilder())->withId(DirectionId::generate())->build();
        $parent = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('parent'))
            ->build($direction);

        (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('child'))
            ->withParent($parent)
            ->build($direction);

        self::assertCount(1, $parent->getChildren());

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('A category cannot be its own parent.');
        $parent->changeParent($parent);
    }
    public function testChangeParentDifferentDirection(): void
    {
        $safetyDirection = (new DirectionBuilder())
            ->withTitle('Охрана труда')
            ->withId(DirectionId::generate())->build();

        $fireDirection = (new DirectionBuilder())
            ->withTitle('Пожарная безопасность')
            ->withId(DirectionId::generate())->build();

        $parent = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('parent'))
            ->build($fireDirection);

        $anotherParent = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('another-parent'))
            ->build($safetyDirection);

        $child = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('child'))
            ->withParent($parent)
            ->build($fireDirection);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Child category cannot be from different direction.');
        $child->changeParent($anotherParent);
    }
    public function testCannotMoveToSubcategory(): void
    {
        $direction = (new DirectionBuilder())->withId(DirectionId::generate())->build();
        $parent = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('parent'))
            ->build($direction);

        $child = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('child'))
            ->withParent($parent)
            ->build($direction);
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Cannot move a category to children category.');

        $parent->changeParent($child);
    }

    public function testUpdateDirectionDirectionSuccess(): void
    {
        $safetyDirection = (new DirectionBuilder())
            ->withId(DirectionId::generate())
            ->withTitle('Охрана труда')
            ->build();

        $fireDirection = (new DirectionBuilder())
            ->withId(DirectionId::generate())
            ->withTitle('Пожарная безопасность')
            ->build();

        $category = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('category'))
            ->build($safetyDirection);

        $category->updateDirection($fireDirection);

        self::assertEquals($fireDirection, $category->getDirection());
    }

    public function testUpdateDirectionItSelf(): void
    {
        $safetyDirection = (new DirectionBuilder())
            ->withId(DirectionId::generate())
            ->withTitle('Охрана труда')
            ->build();

        $category = (new CategoryBuilder())
            ->withCategoryId(CategoryId::generate())
            ->withSlug(Slug::generate('category'))
            ->build($safetyDirection);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('A cannot change the direction to itself');
        $category->updateDirection($safetyDirection);
    }
}