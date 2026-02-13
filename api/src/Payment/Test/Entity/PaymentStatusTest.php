<?php

namespace App\Payment\Test\Entity;

use App\Payment\Entity\PaymentStatus;
use PHPUnit\Framework\TestCase;

class PaymentStatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = new PaymentStatus('succeeded');
        $this->assertEquals('succeeded', $status->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PaymentStatus('invalid');
    }

    public function testPending(): void
    {
        $status = PaymentStatus::pending();
        $this->assertEquals('pending', $status->getValue());
    }
    public function testCancelled(): void
    {
        $status = PaymentStatus::cancelled();
        $this->assertEquals('cancelled', $status->getValue());
    }
    public function testSucceeded(): void
    {
        $status = PaymentStatus::succeeded();
        $this->assertEquals('succeeded', $status->getValue());
    }
}