<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\File;

use App\Distribution\Entity\File\File;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;


final class FileTest extends TestCase
{
    public function testCreate(): void
    {
        $file = new File(
            $id = Uuid::uuid4()->toString(),
            $name = 'file.csv',
            $date = new \DateTimeImmutable(),
        );
        self::assertEquals($id, $file->getId());
        self::assertEquals($name, $file->getName());
        self::assertEquals($date, $file->getDate());
        self::assertFalse($file->isComplete());
    }
}