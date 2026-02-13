<?php

namespace App\Sender\Test\Unit\Entity;

use App\Sender\Entity\MessageStatus;
use PHPUnit\Framework\TestCase;

class MessageStatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = MessageStatus::received();
        self::assertEquals('received', $status->getValue());
    }
    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MessageStatus('invalid');
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MessageStatus('');
    }
    public function testIsReceived(): void
    {
        $status = MessageStatus::received();
        self::assertTrue($status->isReceived());
        self::assertFalse($status->isFailed());
    }

    public function testIsFailed(): void
    {
        $status = MessageStatus::failed();
        self::assertTrue($status->isFailed());
        self::assertFalse($status->isReceived());
    }

}