<?php

namespace App\Direction\Test\Unit\Entity;

use App\Direction\Entity\Category\CategoryId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;


class CategoryIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new CategoryId($value = Uuid::uuid4()->toString());

        $this->assertNotNull($id->getValue());
        $this->assertSame($value, $id->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new CategoryId('invalid');
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new CategoryId('');
    }
    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();
        $id = new CategoryId(mb_strtoupper($value));

        $this->assertSame($value, $id->getValue());
    }
}