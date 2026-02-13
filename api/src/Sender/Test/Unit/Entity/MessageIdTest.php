<?php

namespace App\Sender\Test\Unit\Entity;

use App\Sender\Entity\MessageId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class MessageIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new MessageId($value = Uuid::uuid4()->toString());

        $this->assertNotNull($id->getValue());
        $this->assertSame($value, $id->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MessageId('invalid');
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MessageId('');
    }
    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();
        $id = new MessageId(mb_strtoupper($value));

        $this->assertSame($value, $id->getValue());
    }
}