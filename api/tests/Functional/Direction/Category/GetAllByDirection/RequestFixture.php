<?php

namespace Test\Functional\Direction\Category\GetAllByDirection;

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
        $category = new Category(
            new CategoryId('15823c37-3358-44be-96dc-363d56bde91c'),
            'Служба охраны труда',
            'Собраны комплекты образцов документов по организации на предприятии службы охраны труда',
            'Some simple text',
            new Slug('service'),
            $direction = (new DirectionBuilder())->withId(new DirectionId('37e9c865-8401-4339-bb23-73a25b85e7b3'))->build()
        );

        $manager->persist($direction);

        $manager->persist($category);

        $manager->flush();
    }
}