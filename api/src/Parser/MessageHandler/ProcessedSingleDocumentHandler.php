<?php

declare(strict_types=1);

namespace App\Parser\MessageHandler;

use App\Parser\Event\ProcessedSingleDocument;
use App\Parser\Service\DocumentBodyFetcher;
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
        private readonly DocumentBodyFetcher $documentBodyFetcher,
        private readonly LoggerInterface $logger,
        private readonly DocumentUpserter $upserter,
    ) {
    }

    public function __invoke(ProcessedSingleDocument $event): void
    {
        try {
            $documentHtml = ($this->documentHtmlFetcher)($event->href, $event->cookie);

            $documentBody = ($this->documentBodyFetcher)($documentHtml);

            $this->upserter->upsert(
                $event->categoryId,
                $event->title,
                $event->amount,
                $documentBody
            );
        } catch (\Doctrine\ORM\Exception\EntityManagerClosed $e) {
            throw $e;
        } catch (\Throwable $throwable) {
            $this->logger->error('Ошибка при обработке документа: ' . $throwable->getMessage(), [
                'href' => $event->href,
                'exception' => $throwable
            ]);
        }
    }
}
