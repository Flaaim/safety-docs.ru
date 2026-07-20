<?php

declare(strict_types=1);

namespace App\Parser\Command\Launch;

use App\Flusher;
use App\Parser\Entity\DocumentItem;
use App\Parser\Service\DocumentAttachmentParser;
use App\Parser\Service\DocumentDownloader;
use App\Parser\Service\DocumentHtmlFetcher;
use App\Parser\Service\DocumentListParser;
use App\Parser\Service\RubricatorHtmlFetcher;
use App\Shared\Domain\ValueObject\Currency;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Category\CategoryRepository;
use App\Template\Entity\Document\Amount;
use App\Template\Entity\Document\Document;
use App\Template\Entity\Document\DocumentId;
use App\Template\Entity\Document\DocumentRepository;
use App\Template\Entity\Document\Filename;
use App\Template\Entity\Slug;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Parser\Command\ProcessSingleDocument\Command as ProcessSingleDocumentCommand;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly RubricatorHtmlFetcher $fetchListDocuments,
        private readonly DocumentListParser $documentListParser,
        private readonly CategoryRepository $categories,
        private readonly MessageBusInterface $messageBus,
    ) {
    }


    public function handle(Command $command): void
    {
        $category = $this->categories->findById(new CategoryId($command->categoryId));

        if ($category === null) {
            throw new \DomainException('Category not found.');
        }

        if (count($category->getChildren()) > 0) {
            throw new \DomainException('Cannot add a document, because the current category contains subcategories.');
        }

        $listDocuments = ($this->fetchListDocuments)($command->url, $command->cookie);

        /** @var DocumentItem[] $documents */
        $documents = ($this->documentListParser)($listDocuments);


        foreach ($documents as $document) {
            $this->messageBus->dispatch(
                new ProcessSingleDocumentCommand(
                    $category->getId()->getValue(),
                    $command->amount,
                    $document->title,
                    $document->href,
                    $command->cookie,
                )
            );
        }
    }
}
