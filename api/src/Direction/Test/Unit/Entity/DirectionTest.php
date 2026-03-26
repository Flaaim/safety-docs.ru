<?php

namespace App\Direction\Test\Unit\Entity;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
use PHPUnit\Framework\TestCase;

class DirectionTest extends TestCase
{
    public function testSuccess(): void
    {
        $direction = (new DirectionBuilder())
            ->withId(new DirectionId('2a7a593a-ee23-4a73-bb07-b372438fb269'))
            ->withTitle('title')
            ->withDescription('description')
            ->withSlug(new Slug('slug'))
            ->withText('text')
            ->build();

        self::assertEquals('title', $direction->getTitle());
        self::assertEquals('description', $direction->getDescription());
        self::assertEquals('slug', $direction->getSlug()->getValue());
        self::assertEquals('text', $direction->getText());
        self::assertEquals('2a7a593a-ee23-4a73-bb07-b372438fb269', $direction->getId()->getValue());
    }

    public function testUpdate(): void
    {

        $direction = (new DirectionBuilder())->build();
        $direction->update('title1', 'description1', 'text1', new Slug('slug'));

        self::assertEquals('title1', $direction->getTitle());
        self::assertEquals('description1', $direction->getDescription());
        self::assertEquals('text1', $direction->getText());
        self::assertEquals('slug', $direction->getSlug()->getValue());
    }

    public function testAddCategory(): void
    {
        $direction = (new DirectionBuilder())->build();
        $category = new Category(
            CategoryId::generate(),
            'Category Title',
            'Category Description',
            'Category Text',
            new Slug('cat-slug'),
            $direction
        );
        self::assertCount(1, $direction->getCategories());
        self::assertEquals($category, $direction->getCategories()->first());
    }
    public function testAddExistingCategory(): void
    {
        $direction = (new DirectionBuilder())->build();
        new Category(
            CategoryId::generate(),
            'Category Title',
            'Category Description',
            'Category Text',
            new Slug('cat-slug'),
            $direction
        );
        self::assertCount(1, $direction->getCategories());
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Category with slug cat-slug is exists.');
        new Category(
            CategoryId::generate(),
            'Category Title',
            'Category Description',
            'Category Text',
            new Slug('cat-slug'),
            $direction
        );

    }
    public function testRemoveCategory(): void
    {
        $direction = (new DirectionBuilder())->build();
        new Category(
            CategoryId::generate(),
            'Category Title',
            'Category Description',
            'Category Text',
            $slug = new Slug('cat-slug'),
            $direction
        );
        self::assertCount(1, $direction->getCategories());
        $direction->removeCategory($slug);
        self::assertCount(0, $direction->getCategories());
    }

    public function testRemoveEmptyCategory(): void
    {
        $direction = (new DirectionBuilder())->build();

        self::expectException(\DomainException::class);
        $this->expectExceptionMessage('Category not found in this direction.');

        $direction->removeCategory(new Slug('cat-slug'));
    }

    public function testCanBeDeleted(): void
    {
        $direction = (new DirectionBuilder())->build();

        self::assertTrue($direction->canBeDeleted());

        new Category(
            CategoryId::generate(),
            'Category Title',
            'Category Description',
            'Category Text',
            $slug = new Slug('cat-slug'),
            $direction
        );
        self::assertFalse($direction->canBeDeleted());
    }
}