<?php

namespace App\Shared\Domain\Service\Notification\MessageHandler;

use App\Sender\Event\SendDocumentEmailCommand;
use App\Shared\Domain\Service\Notification\Event\SendTelegramCommand;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendTelegramHandler
{
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger,
        private readonly string $token,
        private readonly string $chatId
    ) {
    }

    public function __invoke(SendTelegramCommand $event): void
    {
        $email = $event->email;
        $price = $event->price;
        $productName = $event->productName;

        try {
            $this->client->request(
                'GET',
                '/bot' . $this->token . '/sendMessage',
                [
                    'form_params' => [
                        'chat_id' => $this->chatId,
                        'text' => 'Успешный платеж. ' . $price  . PHP_EOL
                            . 'Email: ' . $email . PHP_EOL
                            . 'Продукт: ' . $productName . PHP_EOL

                    ]
                ]
            );
        } catch (GuzzleException $e) {
            $this->logger->error('Telegram notifier exception: ' . $e->getMessage());
        }
    }
}
