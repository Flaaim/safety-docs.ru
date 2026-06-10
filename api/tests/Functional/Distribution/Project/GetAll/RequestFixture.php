<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Project\GetAll;

use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    const PROJECT_ID = '12987505-9636-4179-bd5a-dabaa114244d';
    const PROJECT_NAME = 'Блог охраны труда';
    public function load(ObjectManager $manager): void
    {
        $project = new Project(
          new ProjectId(self::PROJECT_ID),
          self::PROJECT_NAME
        );

        $manager->persist($project);

        $manager->flush();
    }
}