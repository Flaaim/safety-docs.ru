<?php

namespace App\Sender\Test\Unit\Entity;

use App\Product\Entity\FileInterface;
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
        $file = $this->createMock(FileInterface::class);

        $file->expects($this->once())->method('exists')->willReturn(true);

        $recipient->addAttachment($file);

        self::assertEquals($file, $recipient->getAttachments()[0]);
        self::assertCount(1, $recipient->getAttachments());
    }

    public function testAddAttachmentFailed(): void
    {
        $recipient = new Recipient($email = new EmailMessage('test@email.ru'), $subject = 'Тестовая отправка');
        $file = $this->createMock(FileInterface::class);

        $file->expects($this->once())->method('exists')->willReturn(false);
        $file->expects($this->once())->method('getValue')->willReturn('test_file.txt');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage("File 'test_file.txt' does not exists.");
        $recipient->addAttachment($file);
    }
}