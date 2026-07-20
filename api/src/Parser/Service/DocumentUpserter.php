<?php

declare(strict_types=1);

namespace App\Parser\Service;

use App\Flusher;
use App\Shared\Domain\ValueObject\Currency;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Category\CategoryRepository;
use App\Template\Entity\Document\Amount;
use App\Template\Entity\Document\Document;
use App\Template\Entity\Document\DocumentId;
use App\Template\Entity\Document\DocumentRepository;
use App\Template\Entity\Document\Filename;
use App\Template\Entity\Slug;

final class DocumentUpserter
{
    /** @psalm-suppress PossiblyUnusedMethod  */
    public function __construct(
        private readonly DocumentRepository $documents,
        private readonly CategoryRepository $categories,
        private readonly DocumentDownloader $downloader,
        private readonly Flusher $flusher,
    ) {
    }

    public function upsert(string $categoryId, string $title, float $amount, string $downloadUrl, string $cookie): void
    {
        $category = $this->categories->findById(new CategoryId($categoryId));
        if ($category === null) {
            throw new \DomainException('Category not found.');
        }

        $existing = $this->documents->findByCategoryIdAndName($category->getId(), $title);

        if ($existing !== null) {
            $this->downloader->replace(
                $existing->getId()->getValue(),
                $existing->getFilename()->getValue(),
                $downloadUrl,
                $cookie
            );
            $existing->refreshUploadedAt();
        } else {
            $documentId = DocumentId::generate();

            $filename = $this->downloader->download($documentId->getValue(), $downloadUrl, $cookie);

            $document = new Document(
                $documentId,
                $title,
                new Amount($amount, new Currency('RUB')),
                new Filename($filename),
                Slug::generate($title)->getValue(),
                $category
            );

            $this->documents->add($document);
        }

        $this->flusher->flush();

        $this->flusher->clear();
    }
}
