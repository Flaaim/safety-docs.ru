<?php

namespace Test\Functional\Template\Delete;

use App\Template\Entity\Direction\DirectionId;
use App\Template\Test\Builder\DirectionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $directionSafety = (new DirectionBuilder())
            ->withId(new DirectionId('9dc41818-1c99-4b3c-b1bc-7c64ee7a0948'))
            ->withTitle('Охрана труда')
            ->withDescription('Охрана труда описание')
            ->withText('Текст охраны труда описание')
            ->build();

        $manager->persist($directionSafety);

        $manager->flush();
    }
}