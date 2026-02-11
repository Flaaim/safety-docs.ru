<?php

namespace App\Sender\Test\Unit\Service\Message;

use App\Payment\Entity\Email;
use App\Product\Entity\File;
use App\Product\Test\TempDir;
use App\Sender\Entity\Recipient;
use App\Sender\Service\Message\MessageCreator;
use App\Shared\Domain\Service\Template\RootPath;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Twig\Environment;

class MessageCreatorTest extends TestCase
{
    private readonly TempDir $tempDir;
    private readonly File $file;

    public function setUp(): void
    {
        $this->tempDir = TempDir::create();
        $rootPath = new RootPath($this->tempDir->getValue());

        $this->file = new File($this->tempFile());
        $this->file->mergeRoot($rootPath);
    }

    public function testSuccess(): void
    {
        $recipient = new Recipient(new Email('user@email.ru'), 'subject');

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
        $recipient = new Recipient(new Email('user@email.ru'), 'subject');
        $recipient->addAttachment($this->file);

        $creator = new MessageCreator(
            $this->createMock(Environment::class),
            'mail/template.html.twig'
        );

        $message = $creator->create($recipient);
        self::assertEquals([new DataPart(new \Symfony\Component\Mime\Part\File($this->file->getFile()))], $message->getAttachments());
    }

    private function tempFile(): string
    {
        $tempFile = tempnam($this->tempDir->getValue(), 'test');
        return basename($tempFile);
    }

    public function tearDown(): void
    {
        $this->tempDir->clear();
    }
}