<?php

namespace Test\Functional\Template\Category\GetAllByDirection;

use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;
use App\Template\Test\Builder\DirectionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{
    public const DIRECTION_ID = '37e9c865-8401-4339-bb23-73a25b85e7b3';
    public const DIRECTION_NOT_FOUND_ID = '1e31cf37-7f99-4698-bfea-ec0be8a0bf00';
    public function load(ObjectManager $manager): void
    {
        $category = new Category(
            new CategoryId('15823c37-3358-44be-96dc-363d56bde91c'),
            'Служба охраны труда',
            'Собраны комплекты образцов документов по организации на предприятии службы охраны труда',
            'Some simple text',
            (new Slug('service'))->getValue(),
            $direction = (new DirectionBuilder())->withId(new DirectionId(self::DIRECTION_ID))->build()
        );
        $manager->persist($category);
        $manager->persist($direction);

        $manager->flush();
    }
}