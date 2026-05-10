<?php

namespace Test\Functional\Direction\GetBySlug;

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
            new DirectionId('a597bffd-cdbe-4ac2-b565-639e96957977'),
            $title = 'Охрана труда',
            'Собраны комплекты документов',
            'some text',
            Slug::generate($title)
        );
        $manager->persist($direction);

        $manager->flush();
    }
}