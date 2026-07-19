<?php

declare(strict_types=1);

namespace App\Parser\Service;

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

final class RubricatorHtmlFetcher
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(string $url, string $cookie): string
    {
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Cookie' => $cookie,
                ]
            ]);
            return $response->getBody()->getContents();
        } catch (\Throwable $throwable) {
            $this->logger->error('Не удалось получить HTML страницы рубрикатора: ' . $throwable->getMessage());
            throw new \DomainException('Ошибка при загрузке страницы.', 0, $throwable);
        }
    }
}
