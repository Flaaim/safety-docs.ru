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

final class Handler
{
    public function __construct(
        private readonly RubricatorHtmlFetcher $fetchListDocuments,
        private readonly DocumentListParser $documentListParser,
        private readonly DocumentHtmlFetcher $documentHtmlFetcher,
        private readonly DocumentAttachmentParser $attachmentParser,
        private readonly DocumentDownloader $downloader,
        private readonly CategoryRepository $categories,
        private readonly DocumentRepository $documents,
        private readonly Flusher $flusher,
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

        $documentHtml = ($this->documentHtmlFetcher)($documents[0], $command->cookie);
        $hrefLink = ($this->attachmentParser)($documentHtml);

        $existing = $this->documents->findByCategoryIdAndName($category->getId(), $documents[0]->title);
        if ($existing !== null) {
            $this->downloader->replace(
                $existing->getId()->getValue(),
                $existing->getFilename()->getValue(),
                $hrefLink,
                $command->cookie
            );
            $existing->refreshUploadedAt();
        } else {
            $documentId = DocumentId::generate();

            $filename = $this->downloader->download($documentId->getValue(), $hrefLink, $command->cookie);

            $document = new Document(
                $documentId,
                $documents[0]->title,
                new Amount($command->amount, new Currency('RUB')),
                new Filename($filename),
                Slug::generate($documents[0]->title)->getValue(),
                $category
            );

            $this->documents->add($document);
        }



        $this->flusher->flush();
//        foreach ($documents as $document){
//            $documentHtml = ($this->downloader)($document, $cookie);
//        }
    }
}
