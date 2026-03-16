<?php

namespace App\Direction\Test\Unit\Entity;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\CategoryBuilder;
use App\Direction\Test\Builder\DirectionBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Arr;
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
        $direction->addCategory($category);
        self::assertCount(1, $direction->getCategories());
        self::assertEquals($category, $direction->getCategories()->first());
    }

    public function testRemoveCategory(): void
    {
        $category1 = (new CategoryBuilder())
            ->withCategoryId($categoryId1 = new CategoryId('fe4140a0-1e7f-420c-b5d1-9fd44f919149'))->build();

        $category2 = (new CategoryBuilder())
            ->withCategoryId($categoryId2 = new CategoryId('5764bba3-dd03-4fe9-b188-98e4c40ecb94'))->build();

        $direction = $category1->getDirection();
        $direction->addCategory($category2);

        self::assertCount(2, $direction->getCategories());
        $direction->removeCategory($categoryId1);
        self::assertCount(1, $direction->getCategories());
    }

    public function testRemoveEmptyCategory(): void
    {
        $direction = (new DirectionBuilder())->build();
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Category not found in this direction.');

        $direction->removeCategory(CategoryId::generate());
    }

}