<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Project\GetAll;

use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $project = new Project(
          ProjectId::generate(),
          'Блог охраны труда'
        );

        $manager->persist($project);

        $manager->flush();
    }
}