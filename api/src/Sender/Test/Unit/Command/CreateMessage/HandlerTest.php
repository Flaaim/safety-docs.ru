<?php

namespace App\Sender\Test\Unit\Command\CreateMessage;

use App\Flusher;
use App\Sender\Command\DeliverMessage\Create\Command;
use App\Sender\Command\DeliverMessage\Create\Handler;
use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\Recipient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use App\Sender\Command\DeliverMessage\Send\Handler as SendHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;

#[CoversClass(Handler::class)]
class HandlerTest extends TestCase
{
    public function testSuccess(): void
    {
        $recipient = new Recipient(new EmailMessage('test@email.ru'), 'Тест');
        $command = new Command($recipient);

        $handler = new Handler(
            $messages = $this->createMock(MessageRepository::class),
            $flusher =$this->createMock(Flusher::class),
            $sendHandler = $this->createMock(SendHandler::class),
            $logger = $this->createMock(LoggerInterface::class)
        );

        $messages->expects($this->once())->method('create')->with(
            $this->isInstanceOf(Message::class)
        );

        $sendHandler->expects($this->once())->method('handle')->with(
            $this->equalTo($recipient)
        );

        $messages->expects($this->once())->method('update')->with(
            $this->isInstanceOf(Message::class)
        );

        $flusher->expects($this->once())->method('flush');


        $handler->handle($command);
    }

    public function testFailed(): void
    {
        $recipient = new Recipient(new EmailMessage('test@email.ru'), 'Тест');
        $command = new Command($recipient);

        $handler = new Handler(
            $messages = $this->createMock(MessageRepository::class),
            $flusher =$this->createMock(Flusher::class),
            $sendHandler = $this->createMock(SendHandler::class),
            $logger = $this->createMock(LoggerInterface::class)
        );

        $messages->expects($this->once())->method('create')->with(
            $this->isInstanceOf(Message::class)
        );

        $sendHandler->expects($this->once())->method('handle')->with(
            $this->equalTo($recipient)
        )->willThrowException(new TransportException('Transport Exception'));

        $logger->expects($this->once())->method('error')->with(
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

        $messages->expects($this->once())->method('update')->with(
            $this->isInstanceOf(Message::class)
        );

        $flusher->expects($this->once())->method('flush');

        $handler->handle($command);
    }
}