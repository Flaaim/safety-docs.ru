<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Project\UnsubscribeContact;

use App\Distribution\Entity\Project\DTO\ContactDTO;
use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const PROJECT_ID = '067cc559-7fa7-4d9d-b747-9b0d7d3382e0';
    public function load(ObjectManager $manager): void
    {
       $project = new Project(
            new ProjectId(self::PROJECT_ID),
           'Блог охраны труда'
       );

       $project->import([new ContactDTO('one@mail.ru'), new ContactDTO('two@mail.ru')]);

       $manager->persist($project);

       $manager->flush();
    }
}