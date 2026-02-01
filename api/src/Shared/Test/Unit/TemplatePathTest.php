<?php

namespace App\Shared\Test\Unit;

use App\Shared\Domain\Service\Template\TemplatePath;
use PHPUnit\Framework\TestCase;

class TemplatePathTest extends TestCase
{
    public function testSuccess(): void
    {
        $file = new TemplatePath(sys_get_temp_dir());
        $this->assertEquals(sys_get_temp_dir(), $file->getValue());
    }
    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TemplatePath('');
    }
}