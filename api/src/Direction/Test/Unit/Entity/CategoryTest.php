<?php

namespace App\Direction\Test\Unit\Entity;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\CategoryBuilder;
use App\Direction\Test\Builder\DirectionBuilder;
use App\Product\Test\ProductBuilder;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testCategory(): void
    {
        $direction = $this->getDirection();
        $serviceCategory = $this->getCategory($direction);

        self::assertEquals('Служба охраны труда', $serviceCategory->getTitle());
        self::assertEquals('Служба охраны труда, комплект документов', $serviceCategory->getDescription());

        self::assertNull($serviceCategory->getParent());
    }
    public function testCategoryWithParent(): void
    {
        $direction = $this->getDirection();
        $parent = $this->getCategory($direction);

        $serviceCategory = new Category(
            CategoryId::generate(),
            'Title',
            'Description',
            'Text',
            new Slug('title'),
            $direction,
            $parent
        );

        self::assertTrue($serviceCategory->isChild());
    }
    public function testCategoryTheOwnParent(): void
    {
        $safetyDirection = $this->getDirection();
        $parent = $this->getCategory($safetyDirection);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('A category cannot be its own parent.');

        new Category(
            $parent->getId(),
            'Title',
            'Description',
            'Text',
            new Slug('title'),
            $safetyDirection,
            $parent
        );

    }
    public function testCategoryDifferentDirection(): void
    {
        $safetyDirection = $this->getDirection('9300fdba-c736-4060-9206-4422bc652c08', 'Safety');
        $fireDirection = $this->getDirection('ab00c25c-5cf8-4ed0-b7eb-54f2cc8541a9', 'Fire');

        $parentCategory = new Category(
            CategoryId::generate(),
            'Parent category',
            'Parent description category',
            'Text',
            new Slug('title'),
            $safetyDirection,
        );

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Child category cannot be from different direction.');

        new Category(
            CategoryId::generate(),
            'Child category',
            'Child description category',
            'Text',
            new Slug('title'),
            $fireDirection,
            $parentCategory
        );

    }
    public function testUpdateParent(): void
    {
        $safetyDirection = $this->getDirection('4c075222-bef7-48d2-9cdf-efd7f58b226b', 'Safety');
        $category = $this->getCategory($safetyDirection);

        $fireDirection = $this->getDirection('171af8ca-86f0-452f-b94b-5b62cc72998a', 'Fire');

        $category->update(
            'Обучение по пожарной безопасности',
            'Обучение по пожарной безопасности, комплект документов',
            'Some text',
            new Slug('education'),
            $fireDirection
        );

        self::assertEquals('Обучение по пожарной безопасности', $category->getTitle());
        self::assertEquals('Обучение по пожарной безопасности, комплект документов', $category->getDescription());
        self::assertEquals('Some text', $category->getText());
        self::assertEquals('education', $category->getSlug()->getValue());
        self::assertEquals('171af8ca-86f0-452f-b94b-5b62cc72998a', $category->getDirection()->getId()->getValue());
        self::assertEquals('Fire', $category->getDirection()->getTitle());
    }

    public function testUpdateChildren(): void
    {
        $safetyDirection = $this->getDirection('9300fdba-c736-4060-9206-4422bc652c08', 'Safety');
        $fireDirection = $this->getDirection('ab00c25c-5cf8-4ed0-b7eb-54f2cc8541a9', 'Fire');

        $parentCategory = $this->getCategory($safetyDirection, 'parent');
        $childCategory = $this->getCategory($safetyDirection, 'children', $parentCategory);

        $childCategory->update(
            'New title',
            'New description',
            'New text',
            new Slug('title'),
            $fireDirection
        );

        self::assertEquals('New title', $childCategory->getTitle());
        self::assertEquals('New description', $childCategory->getDescription());
        self::assertEquals('New text', $childCategory->getText());
    }
    public function testUpdateUpdateChildrenWithDifferentDirection(): void
    {
        $safetyDirection = $this->getDirection('9300fdba-c736-4060-9206-4422bc652c08', 'Safety');
        $fireDirection = $this->getDirection('ab00c25c-5cf8-4ed0-b7eb-54f2cc8541a9', 'Fire');

        $category1 = $this->getCategory($safetyDirection, 'parent');

        $category2 = $this->getCategory($safetyDirection, 'children');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Child category cannot be from different direction.');

        $category2->update(
            'New title',
            'New description',
            'New text',
            new Slug('title'),
            $fireDirection,
            $category1
        );
    }
    public function testCannotMoveCategoryWithChildren(): void
    {
    }
    public function testAssignChild(): void
    {
        $safetyDirection = $this->getDirection('9300fdba-c736-4060-9206-4422bc652c08', 'Safety');
        $parentCategory1 = (new CategoryBuilder())->build($safetyDirection);

        $parentCategory2 = $this->getCategory($safetyDirection, 'parentCategory2');
        $parentCategory3 = $this->getCategory($safetyDirection, 'parentCategory3');

        $parentCategory1->assignChildren($parentCategory2);
        $parentCategory1->assignChildren($parentCategory3);

        self::assertCount(2, $parentCategory1->getChildren());
    }
    public function testAssignChildrenAlready(): void
    {
        $safetyDirection = $this->getDirection('9300fdba-c736-4060-9206-4422bc652c08', 'Safety');
        $categoryId1 = new CategoryId('727d77c0-fef1-443a-9487-60d5a61404f8');

        $child1 = (new CategoryBuilder())->withSlug(new Slug('child'))->withCategoryId($categoryId1)->build($safetyDirection);

        $parentCategory1 = (new CategoryBuilder())
            ->withChildren([$child1])
            ->build($safetyDirection);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('A category child already assigned.');
        $parentCategory1->assignChildren($child1);
    }
    public function testUpdateRefuseParent(): void
    {
        $safetyDirection = $this->getDirection('9300fdba-c736-4060-9206-4422bc652c08', 'Safety');
        $fireDirection = $this->getDirection('ab00c25c-5cf8-4ed0-b7eb-54f2cc8541a9', 'Fire');

        $parentCategory = $this->getCategory($safetyDirection, 'parent');
        $child = $this->getCategory($safetyDirection, 'children', $parentCategory);

        $child->update(
            'New title',
            'New description',
            'New text',
            new Slug('title'),
            $fireDirection,
        );

        self::assertNull($child->getParent());
        self::assertEquals('ab00c25c-5cf8-4ed0-b7eb-54f2cc8541a9', $child->getDirection()->getId()->getValue());
    }
    public function testAssign(): void
    {
        $direction = $this->getDirection();
        $parentCategory = $this->getCategory($direction);
        $childCategory = $this->getCategory($direction, 'children', $parentCategory);
        $product = (new ProductBuilder())->build();

        self::assertNull($childCategory->getProduct());
        $childCategory->assignProduct($product);

        self::assertEquals($product, $childCategory->getProduct());
    }
    public function testAssignAlready(): void
    {
        $direction = $this->getDirection();
        $parentCategory = $this->getCategory($direction);
        $childCategory = $this->getCategory($direction, 'children', $parentCategory);
        $product = (new ProductBuilder())->build();

        $childCategory->assignProduct($product);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product already assigned. You must delete it first.');

        $childCategory->assignProduct($product);

    }
    public function testAssignParentCategory(): void
    {
        $direction = $this->getDirection();
        $parentCategory = $this->getCategory($direction, 'parent');

        $product = (new ProductBuilder())->build();

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Product can be assigned to only child category.');
        $parentCategory->assignProduct($product);
    }
    public function testRefuseNotAssigned(): void
    {
        $direction = $this->getDirection();
        $category = $this->getCategory($direction);

        self::expectException(\DomainException::class);
        $category->refuseProduct();
    }
    public function testRefuse(): void
    {
        $direction = $this->getDirection();
        $parentCategory = $this->getCategory($direction);
        $childCategory = $this->getCategory($direction, 'children', $parentCategory);

        $product = (new ProductBuilder())->build();
        $childCategory->assignProduct($product);
        self::assertEquals($product, $childCategory->getProduct());

        $childCategory->refuseProduct();

        self::assertNull($childCategory->getProduct());
    }
    private function getCategory(Direction $direction, string $slug = 'service', ?Category $parent = null): Category
    {
        return new Category(
            CategoryId::generate(),
            'Служба охраны труда',
            'Служба охраны труда, комплект документов',
            'some text',
            new Slug($slug),
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





}