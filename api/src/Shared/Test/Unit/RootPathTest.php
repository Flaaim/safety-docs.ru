<?php

namespace App\Shared\Test\Unit;

use App\Shared\Domain\Service\Template\RootPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RootPath::class)]
class RootPathTest extends TestCase
{
    public function testSuccess(): void
    {
        $file = new RootPath(sys_get_temp_dir());
        $this->assertEquals(sys_get_temp_dir(), $file->getValue());
    }
    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new RootPath('');
    }

    public function testTrimSlash(): void
    {
        $file = new RootPath(sys_get_temp_dir().'/');
        $this->assertEquals(sys_get_temp_dir(), $file->getValue());
    }
}