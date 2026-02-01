<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testSuccess(): void
    {
        $file = new File('/ppe/template.rar');
        $this->assertEquals('ppe/template.rar', $file->getPathToFile());
    }
    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new File('');
    }
}