<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\Slug;
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
        $slug = new Slug('MY-URL');
        self::assertEquals('my-url', $slug->getValue());
    }

    public function testEquals(): void
    {
        $slug = new Slug('my-url');

        $this->assertTrue($slug->equals(new Slug('my-url')));
        $this->assertTrue($slug->equals(new Slug('My-url')));
        $this->assertTrue($slug->equals($slug));
    }
}