<?php

namespace App\Sender\Test\Unit\Service\Message;

use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Recipient;
use App\Sender\Service\Message\MessageCreator;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Twig\Environment;

class MessageCreatorTest extends TestCase
{

    public function testSuccess(): void
    {
        $recipient = new Recipient(new EmailMessage('user@email.ru'), 'subject');

        $creator = new MessageCreator(
            $twig = $this->createMock(Environment::class),
            $template = 'mail/template.html.twig'
        );
        $expectedHtml = '<h1>Тест email</h1>';

        $twig->expects($this->once())->method('render')->with($template)->willReturn($expectedHtml);

        $message = $creator->create($recipient);

        self::assertEquals([new Address($recipient->getEmail()->getValue())], $message->getTo());
        self::assertEquals($recipient->getSubject(), $message->getSubject());
        self::assertEquals($expectedHtml, $message->getHtmlBody());
    }

    public function testAttachment(): void
    {
        $recipient = new Recipient(new EmailMessage('user@email.ru'), 'subject');
        $recipient->addAttachment($attachment = 'attachment');

        $creator = new MessageCreator(
            $this->createMock(Environment::class),
            'mail/template.html.twig'
        );

        $message = $creator->create($recipient);
        self::assertEquals([new DataPart(new \Symfony\Component\Mime\Part\File($attachment))], $message->getAttachments());
    }

}