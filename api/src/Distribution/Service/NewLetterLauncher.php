<?php

declare(strict_types=1);

namespace App\Distribution\Service;

use App\Distribution\Entity\Project\Contact;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

final class NewLetterLauncher implements NewsletterLauncherInterface
{
    private const BATCH_SIZE = 100;
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger,
        private readonly string $apiKey
    ) {}
    public function launch(array $contacts, string $templateId, string $subject): void
    {
        $requestBody = [
            "message" => [
                'recipients' => $contacts,
                "template_id" => $templateId,
                "skip_unsubscribe" => 0,
                "subject" => $subject,
            ],
        ];
        try {
            $this->client->request('POST', 'email/send.json', [
                'headers' => [
                    'X-API-KEY' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestBody,
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error('Error sending distribution' . $e->getMessage());
            throw $e;
        }

    }

}