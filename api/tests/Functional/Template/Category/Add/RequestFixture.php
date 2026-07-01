<?php

namespace Test\Functional\Template\Category\Add;

use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;
use App\Template\Test\Builder\DirectionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $category = new Category(
            new CategoryId('15823c37-3358-44be-96dc-363d56bde91c'),
            $title = 'Служба охраны труда',
            'Собраны комплекты образцов документов по организации на предприятии службы охраны труда',
            'Some simple text',
            Slug::generate($title)->getValue(),
            $direction = (new DirectionBuilder())->withId(new DirectionId('37e9c865-8401-4339-bb23-73a25b85e7b3'))->build()
        );

        $manager->persist($direction);

        $manager->persist($category);

        $manager->flush();
    }
}