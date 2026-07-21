<?php

declare(strict_types=1);

namespace App\Parser\Service;

use App\Parser\Entity\DocumentAttachment;
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

    public function __invoke(string $htmlContent): ?DocumentAttachment
    {
        try {
            $document = new Document($htmlContent);

            $link = $document->first('a[data-qa-locator="link"][download]');
            if ($link) {
                $originalName = $link->getAttribute('download');
                $url = $link->getAttribute('href');
                if ($originalName === null || $url === null) {
                    return null;
                }
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                return new DocumentAttachment(
                    $url,
                    $extension ?: 'docx',
                );
            }

            return null;
        } catch (\Throwable $throwable) {
            $this->logger->error('Ошибка при парсинге вложений документа: ' . $throwable->getMessage());
            throw new \DomainException('Не удалось извлечь ссылки на скачивание файлов.', 0, $throwable);
        }
    }
}
