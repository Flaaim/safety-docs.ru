<?php

namespace App\Sender\Test\Unit\Command\Send;

use App\Payment\Entity\Email;
use App\Sender\Command\Send\Handler;
use App\Sender\Entity\Recipient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;
use Symfony\Component\Mime\Email as SymfonyEmail;
class HandlerTest extends TestCase
{
    public function testSuccessSend(): void
    {
        $recipient = new Recipient(new Email('test@email.ru'), 'Тестовая отправка письма');

        $handler = new Handler(
            $mailer = $this->createMock(MailerInterface::class),
            $twig = $this->createMock(Environment::class),
            $this->createMock(LoggerInterface::class),
        );

        $mailer->expects(self::once())->method('send')->with(
            $this->equalTo((new SymfonyEmail())
                ->subject($recipient->getSubject())
                ->to($recipient->getEmail()->getValue())
                ->html($twig->render('mail/template.html.twig'))
            )
        )->willReturnCallback(static function ($message) use ($recipient, $twig) {
            /** @var SymfonyEmail $message */
            self::assertEquals($recipient->getSubject(), $message->getSubject());
            self::assertEquals([new Address($recipient->getEmail()->getValue())], $message->getTo());
            self::assertEquals($twig->render('mail/template.html.twig'), $message->getHtmlBody());
        });

        $handler->handle($recipient);
    }

    public function testFailedSend(): void
    {
        $recipient = new Recipient(new Email('test@email.ru'), 'Тестовая отправка письма');

        $handler = new Handler(
            $mailer = $this->createMock(MailerInterface::class),
            $twig = $this->createMock(Environment::class),
            $logger = $this->createMock(LoggerInterface::class),
        );

        $mailer->expects(self::once())->method('send')->willThrowException(new TransportException($message ='TransportException'));

        $logger->expects(self::once())->method('error')->with(
            $this->equalTo('Failed to send mail: '),
            $this->equalTo(['error' => $message])
        );

        self::expectException(TransportException::class);

        $handler->handle($recipient);
    }
}