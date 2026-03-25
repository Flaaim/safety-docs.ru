<?php

namespace Test\Functional\Direction\Delete;

use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;
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
            ->withSlug(new Slug('safety'))
            ->build();

        $manager->persist($directionSafety);

        $manager->flush();
    }
}