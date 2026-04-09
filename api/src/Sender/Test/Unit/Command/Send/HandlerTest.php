<?php

namespace App\Sender\Test\Unit\Command\Send;

use App\Flusher;
use App\Sender\Command\Send\Command;
use App\Sender\Command\Send\Handler;
use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\Recipient;
use App\Sender\Service\Message\CreatorInterface;
use MessageBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

#[CoversClass(Handler::class)]
class HandlerTest extends TestCase
{

    private MessageRepository $messages;
    private LoggerInterface $logger;
    private Flusher $flusher;
    private CreatorInterface $creator;
    private MailerInterface $mailer;

    private Handler $handler;

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->flusher = $this->createMock(Flusher::class);
        $this->creator = $this->createMock(CreatorInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->messages = $this->createMock(MessageRepository::class);

        $this->handler = new Handler(
            $this->messages,
            $this->flusher,
            $this->logger,
            $this->creator,
            $this->mailer
        );
    }
    public function testSuccess(): void
    {
        $recipient = new Recipient(new EmailMessage('test@email.ru'), 'Тест');
        $command = new Command($recipient);

        $this->messages->expects($this->once())->method('add')->with(
            self::callback(function (Message $message): bool {
                self::assertEquals('test@email.ru', $message->getRecipient()->getEmail()->getValue());
                self::assertEquals('Тест', $message->getRecipient()->getSubject());
                return true;
        }));

        $this->creator->expects($this->once())->method('create')->with(
            $this->equalTo($recipient),
        )->willReturn($mimeMessage = new \Symfony\Component\Mime\Email());

        $this->mailer->expects($this->once())->method('send')->with($mimeMessage);

        $this->messages->expects($this->once())->method('update')->with(
            $this->isInstanceOf(Message::class)
        );

        $this->flusher->expects($this->once())->method('flush');

        $this->handler->handle($command);
    }

    public function testFailed(): void
    {
        $recipient = new Recipient(new EmailMessage('test@email.ru'), 'Тест');
        $command = new Command($recipient);

        $this->messages->expects($this->once())->method('add')->with(
            self::callback(function (Message $message): bool {
                self::assertEquals('test@email.ru', $message->getRecipient()->getEmail()->getValue());
                self::assertEquals('Тест', $message->getRecipient()->getSubject());
                return true;
            }));

        $this->creator->expects($this->once())->method('create')->with(
            $this->equalTo($recipient),
        )->willReturn($mimeMessage = new \Symfony\Component\Mime\Email());

        $this->mailer->expects($this->once())->method('send')->with(
            $this->equalTo($mimeMessage)
        )->willThrowException(new TransportException('Transport Exception'));

        $this->logger->expects($this->once())->method('error')->with(
            $this->equalTo('Failed to send message'),
            $this->callback(function (array $context) use ($recipient) {
                $this->assertArrayHasKey('message_id', $context);
                $this->assertArrayHasKey('recipient', $context);
                $this->assertArrayHasKey('error', $context);
                $this->assertArrayHasKey('exception_class', $context);

                $this->assertEquals($recipient->getEmail()->getValue(), $context['recipient']);
                $this->assertEquals('Transport Exception', $context['error']);
                $this->assertEquals(TransportException::class, $context['exception_class']);

                $this->assertNotEmpty($context['message_id']);
                return true;
            })
        );

        $this->messages->expects($this->once())->method('update')->with(
            $this->isInstanceOf(Message::class)
        );

        $this->flusher->expects($this->once())->method('flush');

        $this->handler->handle($command);
    }
}