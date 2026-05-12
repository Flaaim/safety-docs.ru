<?php

namespace Test\Functional\Direction\Category\Delete;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
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

        $parentCategory = new Category(
            new CategoryId('8aa8f453-b19b-4b53-915b-1f04c83a9aee'),
            $title = 'Комплекты документов',
            'Различные комплекты документов',
            'Some text',
            Slug::generate($title),
            $direction
        );

        $childCategory = new Category(
            new CategoryId('b5640c7c-75f8-41c3-96c1-0444e96b4f5d'),
            $title = 'Комплекты документов по медосмотрам',
            'Различные комплекты документов по медосмотрам',
            'Some text',
            Slug::generate($title),
            $direction,
        );

        $parentCategory->addChild($childCategory);

        $manager->persist($direction);
        $manager->persist($parentCategory);
        $manager->persist($childCategory);

        $manager->flush();
    }
}