<?php

namespace App\Sender\Test\Unit\Entity;

use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Recipient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Recipient::class)]
class RecipientTest extends TestCase
{
    public function testSuccess(): void
    {
        $recipient = new Recipient($email = new EmailMessage('test@email.ru'), $subject = 'Тестовая отправка');

        self::assertEquals($email, $recipient->getEmail());
        self::assertEquals($subject, $recipient->getSubject());
        self::assertEmpty($recipient->getAttachments());
    }
    public function testAddAttachment(): void
    {
        $recipient = new Recipient($email = new EmailMessage('test@email.ru'), $subject = 'Тестовая отправка');
        $filename = 'template100.1.rar';

        $recipient->addAttachment($filename);

        self::assertEquals($filename, $recipient->getAttachments()[0]);
        self::assertCount(1, $recipient->getAttachments());
    }

}