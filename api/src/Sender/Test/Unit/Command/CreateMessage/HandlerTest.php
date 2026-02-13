<?php

namespace App\Sender\Test\Unit\Command\CreateMessage;

use App\Flusher;
use App\Sender\Command\CreateMessage\Command;
use App\Sender\Command\CreateMessage\Handler;
use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageId;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\MessageStatus;
use App\Sender\Entity\Recipient;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    public function testSuccess(): void
    {
        $recipient = new Recipient(new EmailMessage('test@email.ru'), 'Тест');
        $command = new Command($recipient);

        $handler = new Handler(
            $messages = $this->createMock(MessageRepository::class),
            $flusher =$this->createMock(Flusher::class),
        );
        $messages->expects($this->once())->method('create')->with(
            $this->isInstanceOf(Message::class)
        );
        $flusher->expects($this->once())->method('flush');

        $handler->handle($command);
    }
}