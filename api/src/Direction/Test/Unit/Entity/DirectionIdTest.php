<?php

namespace App\Direction\Test\Unit\Entity;

use App\Direction\Entity\DirectionId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;


class DirectionIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new DirectionId($value = Uuid::uuid4()->toString());

        $this->assertNotNull($id->getValue());
        $this->assertSame($value, $id->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DirectionId('invalid');
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DirectionId('');
    }
    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();
        $id = new DirectionId(mb_strtoupper($value));

        $this->assertSame($value, $id->getValue());
    }
}