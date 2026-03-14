<?php

namespace Test\Functional\Direction\GetAll;

use App\Direction\Entity\Direction;
use App\Direction\Entity\DirectionId;
use App\Direction\Entity\Slug;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $direction = new Direction(
            DirectionId::generate(),
            'Охрана труда',
            'Собраны комплекты документов',
            'some text',
            new Slug('safety')
        );
        $manager->persist($direction);

        $manager->flush();
    }
}