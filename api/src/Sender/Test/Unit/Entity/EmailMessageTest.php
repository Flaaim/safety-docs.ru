<?php

namespace App\Sender\Test\Unit\Entity;

use App\Sender\Entity\EmailMessage;
use PHPUnit\Framework\TestCase;

class EmailMessageTest extends TestCase
{
    public function testSuccess(): void
    {
        $email = new EmailMessage($value = 'user@app.test');
        $this->assertSame($value, $email->getValue());
    }

    public function testCase(): void
    {
        $value = 'user@app.ru';
        $email = new EmailMessage(mb_strtoupper($value));
        $this->assertSame($value, $email->getValue());
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new EmailMessage('');
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new EmailMessage('invalid');
    }
}