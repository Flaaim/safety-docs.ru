<?php

namespace App\Direction\Test\Unit\Entity;

use App\Direction\Entity\Slug;
use PHPUnit\Framework\TestCase;

class SlugTest extends TestCase
{
    public function testSuccess(): void
    {
        $slug = new Slug('my-url');

        $this->assertEquals('my-url', $slug->getValue());
    }
    public function testEmpty(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new Slug('');
    }

    public function testCase(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new Slug('MY-URL');
    }
}