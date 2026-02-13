<?php

namespace App\Sender\Test\Unit\Command\Confirm;

use App\Flusher;
use App\Sender\Command\DeliverMessage\Confirm\Handler;
use App\Sender\Entity\EmailMessage;
use App\Sender\Entity\Message;
use App\Sender\Entity\MessageId;
use App\Sender\Entity\MessageRepository;
use App\Sender\Entity\MessageStatus;
use App\Sender\Entity\Recipient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Handler::class)]
class HandlerTest extends TestCase
{
    public function testSuccess(): void
    {
        $message = $this->createMessage();

        $handler = new Handler(
            $messages = $this->createMock(MessageRepository::class),
            $flusher = $this->createMock(Flusher::class)
        );

        $messages->expects(self::once())->method('update')->with(
            $this->equalTo($message),
        );

        $flusher->expects(self::once())->method('flush');

        $handler->handle($message);
    }

    private function createMessage(): Message
    {
        return new Message(
            new MessageId('81bd4a4c-aef7-466a-94e3-bf809d7f9217'),
            new Recipient(new EmailMessage('test@email.ru'), 'Тестовая отправка'),
            MessageStatus::pending(),
            new \DateTimeImmutable()
        );
    }
}