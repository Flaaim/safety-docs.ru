<?php

namespace Test\Functional\Template\GetAll;

use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $direction = new Direction(
            new DirectionId('37e9c865-8401-4339-bb23-73a25b85e7b3'),
            $title = 'Охрана труда',
            'Собраны комплекты документов',
            'some text',
            Slug::generate($title)
        );
        $manager->persist($direction);

        $manager->flush();
    }
}