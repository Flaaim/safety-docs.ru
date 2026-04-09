<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\Filename;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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

        $filename = new Filename($value = 'template100.1.rar');
        self::assertEquals($value, $filename->getValue());
    }
    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Filename('');
    }

    public function testCase(): void
    {
        $value = mb_strtoupper('template100.1.rar');
        $filename = new Filename($value);

        self::assertEquals('template100.1.rar', $filename->getValue());
    }

    #[DataProvider('valuesProvider')]
    public function testInvalid($value): void
    {
        self::expectException(\InvalidArgumentException::class);
        new Filename($value);
    }

    public static function valuesProvider(): array
    {
        return [
            ['template100.1.1.rar'],
            ['template100rar'],
        ];
    }
}