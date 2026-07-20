<?php

declare(strict_types=1);

namespace App\Parser\MessageHandler;

use App\Parser\Event\ProcessedSingleDocument;
use App\Parser\Service\DocumentAttachmentParser;
use App\Parser\Service\DocumentHtmlFetcher;
use App\Parser\Service\DocumentUpserter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final class ProcessedSingleDocumentHandler
{
    /** @psalm-suppress PossiblyUnusedMethod  */
    public function __construct(
        private readonly DocumentHtmlFetcher $documentHtmlFetcher,
        private readonly DocumentAttachmentParser $attachmentParser,
        private readonly LoggerInterface $logger,
        private readonly DocumentUpserter $upserter,
    ) {
    }

    public function __invoke(ProcessedSingleDocument $event): void
    {
        try {
            $documentHtml = ($this->documentHtmlFetcher)($event->href, $event->cookie);

            $downloadUrl = ($this->attachmentParser)($documentHtml);

            if ($downloadUrl === null) {
                $this->logger->error('Parser error. Download link is null' . $event->href);
                return;
            }

            $this->upserter->upsert(
                $event->categoryId,
                $event->title,
                $event->amount,
                $downloadUrl,
                $event->cookie
            );
        } catch (\Throwable $throwable) {
            $this->logger->error('Ошибка при обработке документа: ' . $throwable->getMessage(), [
                'href' => $event->href,
                'exception' => $throwable
            ]);
        }
    }
}
