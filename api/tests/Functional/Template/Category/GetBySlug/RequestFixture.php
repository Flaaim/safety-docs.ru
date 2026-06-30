<?php

namespace Test\Functional\Template\Category\GetBySlug;

use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;
use App\Template\Test\Builder\CategoryBuilder;
use App\Template\Test\Builder\DirectionBuilder;
use App\Product\Entity\ProductId;
use App\Product\Test\ProductBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $direction = (new DirectionBuilder())
            ->withId(new DirectionId('e42b8e4f-0ac3-4cca-984d-4f1dc983e970'))
            ->withTitle('Охрана труда')
            ->withDescription('Охрана труда описание')
            ->withText('Охрана труда текст')
            ->build();

        $emptyCategory = new Category(
            new CategoryId('8aa8f453-b19b-4b53-915b-1f04c83a9aee'),
            'Служба охраны труда',
            'Служба охраны труда - комплект документов',
            'Some text',
            new Slug('service'),
            $direction
        );

        $product = (new ProductBuilder())
            ->withId(new ProductId('bffa46d9-6644-42d9-9c76-1e601c22d40b'))
            ->withName('5 документов медосмотров')
            ->build();

        $parentCategory = (new CategoryBuilder())
            ->withCategoryId(new CategoryId('9582c2ff-e788-46f6-94f9-6b7d73b309bd'))
            ->withSlug(new Slug('parent'))
            ->build($direction);

        $categoryWithProduct = new Category(
            new CategoryId('040794de-7a19-47be-947a-e5ed74b579b8'),
            'Медицинские осмотры',
            'Медицинские осмотры - комплект документов',
            'Some text',
            new Slug('medical'),
            $direction,
            $parentCategory
        );

        $categoryWithProduct->assignProduct($product);

        $manager->persist($categoryWithProduct);

        $manager->persist($direction);

        $manager->persist($emptyCategory);

        $manager->persist($product);

        $manager->flush();
    }
}