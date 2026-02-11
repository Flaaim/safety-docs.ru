<?php

namespace App\Sender\Test\Unit\Command\Send;

use App\Payment\Entity\Email;
use App\Sender\Command\Send\Handler;
use App\Sender\Entity\Recipient;
use App\Sender\Service\Message\CreatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as SymfonyMessage;

#[CoversClass(Handler::class)]
class HandlerTest extends TestCase
{
    public function testSuccessSend(): void
    {
        $recipient = new Recipient(new Email('test@email.ru'), 'Тест');
        $symfonyMessage = $this->getSymfonyMessage($recipient);

        $handler = new Handler(
            $mailer = $this->createMock(MailerInterface::class),
            $this->createMock(LoggerInterface::class),
            $creator = $this->createMock(CreatorInterface::class),
        );

        $creator->expects($this->once())
            ->method('create')->with($recipient)->willReturn($symfonyMessage);

        $mailer->expects(self::once())->method('send')
            ->with($symfonyMessage);

        $handler->handle($recipient);
    }

    public function testFailedSend(): void
    {
        $recipient = new Recipient(new Email('test@email.ru'), 'Тест');
        $symfonyMessage = $this->getSymfonyMessage($recipient);

        $handler = new Handler(
            $mailer = $this->createMock(MailerInterface::class),
            $logger = $this->createMock(LoggerInterface::class),
            $creator = $this->createMock(CreatorInterface::class),
        );

        $creator->expects($this->once())
            ->method('create')->with($recipient)->willReturn($symfonyMessage);

        $mailer->expects(self::once())->method('send')
            ->with($symfonyMessage)->willThrowException(new TransportException('Transport failed.'));

        $logger->expects($this->once())->method('error')->with(
            $this->equalTo('Failed to send mail: '),
            $this->equalTo([
                'error' => 'Transport failed.',
                'recipient' => 'test@email.ru',
            ])
        );

        self::expectException(TransportException::class);
        self::expectExceptionMessage('Transport failed.');
        $handler->handle($recipient);
    }

    private function getSymfonyMessage(Recipient $recipient): SymfonyMessage
    {
        $message = new SymfonyMessage();
        $message->to($recipient->getEmail()->getValue());
        $message->subject($recipient->getSubject());

        return $message;
    }
}