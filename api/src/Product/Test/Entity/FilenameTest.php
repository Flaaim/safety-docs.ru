<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\Filename;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Filename::class)]
class FilenameTest extends TestCase
{
    public function setUp(): void
    {
        $this->tempDir = InMemoryFileSystemPath::create();
    }

    public function testSuccess(): void
    {
        $filename = new Filename('template.rar');
        self::assertEquals('template.rar', $filename->getValue());
    }
    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Filename('');
    }

    public function testCase(): void
    {
        $value = mb_strtoupper('template.rar');
        $filename = new Filename($value);

        self::assertEquals('template.rar', $filename->getValue());
    }

    public function testInvalid(): void
    {

    }
}