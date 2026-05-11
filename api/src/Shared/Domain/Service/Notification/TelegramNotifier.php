<?php

namespace App\Shared\Domain\Service\Notification;

use App\Shared\Domain\Event\Payment\SuccessfulPaymentEvent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class TelegramNotifier implements NotificationInterface
{
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger,
        private readonly string $token,
        private readonly string $chatId
    ) {
    }

    public function notify(object $event): void
    {
        if ($event instanceof SuccessfulPaymentEvent) {
            $this->sendSuccessfulPayment($event);
        }
    }
    public function sendSuccessfulPayment(object $event): void
    {
        $payment = $event->getPayment();
        try {
            $this->client->request(
                'GET',
                '/bot' . $this->token . '/sendMessage',
                [
                    'form_params' => [
                        'chat_id' => $this->chatId,
                        'text' => 'Успешный платеж. ' . PHP_EOL
                            . 'Сумма: ' . $payment->getPrice()->formatted() . PHP_EOL
                            . 'Email: ' . $payment->getEmail()->getValue() . PHP_EOL
                            . 'Продукт: ' . $payment->getProductId() . PHP_EOL

                    ]
                ]
            );
        } catch (GuzzleException $e) {
            $this->logger->error('Telegram notifier exception: ' . $e->getMessage());
        }
    }
}
