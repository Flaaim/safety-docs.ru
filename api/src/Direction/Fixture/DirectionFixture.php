<?php

namespace App\Direction\Fixture;

use App\Direction\Entity\Direction;
use App\Direction\Entity\DirectionId;
use App\Direction\Entity\Slug;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class DirectionFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $direction = new Direction(
            DirectionId::generate(),
            'Direction name',
            'Direction description',
            'lorem ipsum text lorem ipsam text lorem ipsum text lorem ipsum text lorem ipsum text',
            new Slug('safety')
        );

        $manager->persist($direction);
        $manager->flush();
    }
}