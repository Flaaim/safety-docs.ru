<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\File;

use App\Distribution\Entity\File\File;
use App\Distribution\Entity\File\FileId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class FileTest extends TestCase
{
    public function testCreate(): void
    {
        $file = new File(
            $id = new FileId(Uuid::uuid4()->toString()),
            $name = 'file.csv',
            $date = new \DateTimeImmutable(),
        );
        self::assertEquals($id->getValue(), $file->getId());
        self::assertEquals($name, $file->getName());
        self::assertEquals($date, $file->getDate());
        self::assertFalse($file->isComplete());
    }
}
