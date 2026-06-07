<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\File;

use App\Distribution\Entity\File\FileId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class FileIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new FileId($value = Uuid::uuid4()->toString());

        self::assertSame($value, $id->getValue());
    }

    public function testCase(): void
    {
        $value = mb_strtoupper(Uuid::uuid4()->toString());
        $id = new FileId($value);
        self::assertSame(mb_strtolower($value), $id->getValue());
    }
    public function testInvalid(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new FileId('invalid');
    }

    public function testEmpty(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new FileId('');
    }

    public function testEquals(): void
    {
        $id1 = new FileId(Uuid::uuid4()->toString());
        $id2 = new FileId(Uuid::uuid4()->toString());
        self::assertFalse($id1->equals($id2));
        self::assertTrue($id1->equals($id1));
    }
}
