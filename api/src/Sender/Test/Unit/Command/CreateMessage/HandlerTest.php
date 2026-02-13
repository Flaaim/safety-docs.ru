<?php

namespace App\Sender\Test\Unit\Command\CreateMessage;

use App\Flusher;
use App\Sender\Command\DeliverMessage\Create\Command;
use App\Sender\Command\DeliverMessage\Create\Handler;
use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\Recipient;
use App\Shared\Domain\Event\Message\CreateMessageEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
            $dispatcher = $this->createMock(EventDispatcher::class)
        );

        $messages->expects($this->once())->method('create')->with(
            $this->isInstanceOf(Message::class)
        );

        $flusher->expects($this->once())->method('flush');

        $dispatcher->expects($this->once())->method('dispatch')->with(
            $this->isInstanceOf(CreateMessageEvent::class)
        );

        $handler->handle($command);
    }
}