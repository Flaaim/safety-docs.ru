<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\GetContactFiles;

use App\Distribution\Entity\File\File;
use App\Distribution\Entity\File\FileId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const FILE_1 = [
        'id' => 'eeb3650a-3c29-4f36-88e0-a11f915fe5b4',
        'name' => 'file1.csv',
    ];
    public const FILE_2 = [
        'id' => 'd3b91679-3b16-4b94-8520-26802e0da599',
        'name' => 'file2.csv',
    ];
    public function load(ObjectManager $manager): void
    {
        $file1 = new File(
            new FileId(self::FILE_1['id']),
            self::FILE_1['name'],
            new \DateTimeImmutable()
        );

        $manager->persist($file1);

        $file2 = new File(
            new FileId(self::FILE_2['id']),
            self::FILE_2['name'],
            new \DateTimeImmutable()
        );

        $manager->persist($file2);

        $manager->flush();
    }
}