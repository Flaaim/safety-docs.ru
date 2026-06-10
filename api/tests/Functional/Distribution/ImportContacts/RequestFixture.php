<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\ImportContacts;

use App\Distribution\Entity\File\File;
use App\Distribution\Entity\File\FileId;
use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    const FILE_ID = '372dbc3c-cc8e-40ac-8d6c-f975a5480531';
    const PROJECT_ID = '81eee054-c684-4dad-9c64-4da8869ef50a';
    public function load(ObjectManager $manager): void
    {
        $file = new File(
            new FileId(self::FILE_ID),
            'contacts.csv',
            new \DateTimeImmutable(),
        );

        $manager->persist($file);

        $project = new Project(
            new ProjectId(self::PROJECT_ID),
            'Блог охраны труда'
        );

        $manager->persist($project);

        $manager->flush();
    }
}