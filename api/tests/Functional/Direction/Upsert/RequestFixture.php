<?php

namespace Test\Functional\Direction\Upsert;

use App\Direction\Entity\DirectionId;
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

        $directionFire = (new DirectionBuilder())
            ->withId(new DirectionId('9582c2ff-e788-46f6-94f9-6b7d73b309bd'))
            ->withTitle('Пожарная безопасность')
            ->withDescription('Пожарная безопасность описание')
            ->withText('Текст пожарная безопасность')
            ->withSlug(new Slug('fire'))
            ->build();

        $manager->persist($directionFire);

        $manager->flush();
    }
}