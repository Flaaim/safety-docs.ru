<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\ProductId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ProductIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new ProductId($value = Uuid::uuid4()->toString());

        $this->assertNotNull($id->getValue());
        $this->assertSame($value, $id->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ProductId('invalid');
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ProductId('');
    }
    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();
        $id = new ProductId(mb_strtoupper($value));

        $this->assertSame($value, $id->getValue());
    }
}