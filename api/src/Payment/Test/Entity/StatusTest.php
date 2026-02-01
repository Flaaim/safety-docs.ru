<?php

namespace App\Payment\Test\Entity;

use App\Payment\Entity\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = new Status('succeeded');
        $this->assertEquals('succeeded', $status->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $status = new Status('invalid');
    }

    public function testPending(): void
    {
        $status = Status::pending();
        $this->assertEquals('pending', $status->getValue());
    }
    public function testCancelled(): void
    {
        $status = Status::cancelled();
        $this->assertEquals('cancelled', $status->getValue());
    }
    public function testSucceeded(): void
    {
        $status = Status::succeeded();
        $this->assertEquals('succeeded', $status->getValue());
    }
}