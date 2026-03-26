<?php

namespace Test\Functional\Direction\GetAll;

use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $direction = new Direction(
            new DirectionId('37e9c865-8401-4339-bb23-73a25b85e7b3'),
            'Охрана труда',
            'Собраны комплекты документов',
            'some text',
            new Slug('safety')
        );
        $manager->persist($direction);

        $manager->flush();
    }
}