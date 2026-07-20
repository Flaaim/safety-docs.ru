<?php

declare(strict_types=1);

namespace App\Parser\Command\Launch;

use App\Parser\Entity\DocumentItem;
use App\Parser\Service\DocumentListParser;
use App\Parser\Service\RubricatorHtmlFetcher;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Category\CategoryRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Parser\Event\ProcessedSingleDocument;

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
                new ProcessedSingleDocument(
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
