<?php

declare(strict_types=1);

namespace Distribution\Newsletter\Draft;

use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    const PROJECT_ID = '879771e4-8362-4558-81ba-6c53abcc7300';
    public function load(ObjectManager $manager): void
    {
        $project = new Project(
            new ProjectId(self::PROJECT_ID),
            'Блог охраны труда'
        );

        $manager->persist($project);

        $manager->flush();
    }
}