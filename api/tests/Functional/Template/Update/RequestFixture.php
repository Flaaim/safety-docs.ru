<?php

namespace Test\Functional\Template\Update;

use App\Template\Entity\Direction\DirectionId;
use App\Template\Test\Builder\DirectionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $directionIndustrial = (new DirectionBuilder())
            ->withId(new DirectionId('9582c2ff-e788-46f6-94f9-6b7d73b309bd'))
            ->withTitle('Промышленная безопасность')
            ->withDescription('Промышленная безопасность описание')
            ->withText('Текст промышленная безопасность описание')
            ->build();

        $manager->persist($directionIndustrial);


        $directionFire = (new DirectionBuilder())
            ->withId(new DirectionId('9dc41818-1c99-4b3c-b1bc-7c64ee7a0948'))
            ->withTitle('Пожарная безопасность')
            ->withDescription('Пожарная безопасность описание')
            ->withText('Текст пожарная безопасность')
            ->build();

        $manager->persist($directionFire);

        $manager->flush();
    }
}