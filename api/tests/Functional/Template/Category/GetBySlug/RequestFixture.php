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
    public const DIRECTION_ID = 'e42b8e4f-0ac3-4cca-984d-4f1dc983e970';
    public const CATEGORY_ID = '8aa8f453-b19b-4b53-915b-1f04c83a9aee';
    public function load(ObjectManager $manager): void
    {
        $direction = (new DirectionBuilder())
            ->withId(new DirectionId(self::DIRECTION_ID))
            ->withTitle('Охрана труда')
            ->withDescription('Охрана труда описание')
            ->withText('Охрана труда текст')
            ->build();
        $manager->persist($direction);

        $emptyCategory = new Category(
            new CategoryId(self::CATEGORY_ID),
            'Инструкции по охране труда',
            'Различные инструкции по охране труда',
            'Some text',
            (new Slug('instructions'))->getValue(),
            $direction
        );
        $manager->persist($emptyCategory);

        $parentCategory = (new CategoryBuilder())
            ->withCategoryId(new CategoryId('9582c2ff-e788-46f6-94f9-6b7d73b309bd'))
            ->withSlug(new Slug('parent'))
            ->build($direction);
        $manager->persist($parentCategory);

        $manager->flush();
    }
}