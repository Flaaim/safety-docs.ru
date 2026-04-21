<?php

namespace App\Shared\Test\Unit\ValueObject\FileSystem;

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileSystemPath::class)]
class FileSystemPathTest extends TestCase
{
    public function testSuccess(): void
    {
        $file = new FileSystemPath(sys_get_temp_dir());
        $this->assertEquals(sys_get_temp_dir(), $file->getValue());
    }
    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new FileSystemPath('');
    }

    public function testTrimSlash(): void
    {
        $file = new FileSystemPath(sys_get_temp_dir().'/');
        $this->assertEquals(sys_get_temp_dir(), $file->getValue());
    }
}