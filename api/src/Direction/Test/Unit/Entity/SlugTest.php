<?php

namespace App\Direction\Test\Unit\Entity;

use App\Direction\Entity\Slug;
use PHPUnit\Framework\TestCase;

class SlugTest extends TestCase
{
    public function testSuccess(): void
    {
        $slug = Slug::generate('my-url');

        $this->assertEquals('my-url', $slug->getValue());
    }
    public function testEmpty(): void
    {
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Cannot generate slug from the given title.');
        Slug::generate('');
    }

    public function testCase(): void
    {
        $slug = Slug::generate('MY-URL');
        self::assertEquals('my-url', $slug->getValue());
    }

    public function testEquals(): void
    {
        $slug = Slug::generate('my-url');

        $this->assertTrue($slug->equals(Slug::generate('my-url')));
        $this->assertTrue($slug->equals(Slug::generate('My-url')));
        $this->assertTrue($slug->equals($slug));
    }

    public function testTransliterate(): void
    {
        $slug = Slug::generate('Мое название 01');
        self::assertEquals('moe-nazvanie-01', $slug->getValue());
    }
}