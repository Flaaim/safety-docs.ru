<?php

namespace Test\Functional\Direction\Category\GetBySlug;

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
            ->withSlug(new Slug('safety'))
            ->build();

        $category = new Category(
            new CategoryId('8aa8f453-b19b-4b53-915b-1f04c83a9aee'),
            'Служба охраны труда',
            'Служба охраны труда - комплект документов',
            'Some text',
            new Slug('service'),
            $direction
        );

        $manager->persist($direction);

        $manager->persist($category);

        $manager->flush();
    }
}