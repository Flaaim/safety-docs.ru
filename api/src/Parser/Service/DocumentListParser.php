<?php

declare(strict_types=1);

namespace App\Parser\Service;

use App\Parser\Entity\DocumentItem;
use DiDom\Document;
use DiDom\Element;
use Psr\Log\LoggerInterface;

final class DocumentListParser
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(string $content): array
    {
        $document = new Document($content);
        $documents = [];
        try {
            $links = $document->find('.b-list_type_rubricator-content .js-doc-item a');

            foreach ($links as $link) {
                if (!$link instanceof Element) {
                    continue;
                }
                $url = $link->getAttribute('href');
                if ($url === null) {
                    throw new \DomainException('Href is not defined');
                }
                $documents[] = new DocumentItem(
                    trim($link->text()),
                    $this->getLastElements($url)
                );
            }
            return $documents;
        } catch (\Throwable $throwable) {
            $this->logger->error('Не получилось составить массив документов: ' . $throwable->getMessage());
            throw new \DomainException('Ошибка при парсинге списка документов.', 0, $throwable);
        }
    }

    private function getLastElements(string $url): string
    {
        $segments = explode('/', trim($url, '/'));
        $lastTwo = array_slice($segments, -2);
        return implode('/', $lastTwo);
    }
}
