<?php

declare(strict_types=1);

namespace App\Parser\Service;

use DiDom\Document;
use DiDom\Element;
use Psr\Log\LoggerInterface;

final class DocumentAttachmentParser
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(string $htmlContent): ?string
    {
        try {
            $document = new Document($htmlContent);
            /** @var Element[] $links */
            $links = $document->find('a[href^="/api/v2/attachment-file_get"]');

            foreach ($links as $link) {
                if (!$link instanceof Element) {
                    continue;
                }
                $text = trim($link->text());
                if ($text === 'Скачать шаблон') {
                    return $link->getAttribute('href');
                }
            }

            return null;
        } catch (\Throwable $throwable) {
            $this->logger->error('Ошибка при парсинге вложений документа: ' . $throwable->getMessage());
            throw new \DomainException('Не удалось извлечь ссылки на скачивание файлов.', 0, $throwable);
        }
    }
}
