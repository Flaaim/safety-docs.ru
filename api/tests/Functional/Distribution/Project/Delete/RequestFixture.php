<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Project\Delete;

use App\Distribution\Entity\Project\DTO\ContactDTO;
use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const PROJECT_ID = 'a2f222c6-8ccc-42aa-8ff5-5a13464d73aa';
    public function load(ObjectManager $manager): void
    {
        $project = new Project(
            new ProjectId(self::PROJECT_ID),
            'Блог охраны труда'
        );

        $project->import([new ContactDTO('test1@email.ru'), new ContactDTO('test2@mail.ru')]);

        $manager->persist($project);
        $manager->flush();
    }
}