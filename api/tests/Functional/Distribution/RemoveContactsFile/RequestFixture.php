<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\RemoveContactsFile;

use App\Distribution\Entity\File\File;
use App\Distribution\Entity\File\FileId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    const FILENAME = 'contacts.csv';
    const FILE_ID = '4344fae1-8d0b-4371-8644-69d8c99eda90';
    public function load(ObjectManager $manager): void
    {

        $file = new File(
            new FileId(self::FILE_ID),
            self::FILENAME,
            new \DateTimeImmutable(),
        );

        $manager->persist($file);

        $manager->flush();
    }
}