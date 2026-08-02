<?php

declare(strict_types=1);

namespace App\Parser\Service;

use App\Flusher;
use App\Shared\Domain\ValueObject\Currency;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
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
        private readonly DocumentAiRewriter $aiRewriter,
        private readonly FileSystemPath $fileSystemPath,
        private readonly Flusher $flusher,
    ) {
    }

    public function upsert(string $categoryId, string $title, float $amount, string $documentHtml): void
    {
        $category = $this->categories->findById(new CategoryId($categoryId));
        if ($category === null) {
            throw new \DomainException('Category not found.');
        }

        $existingDocument = $this->documents->findByCategoryIdAndName($category->getId(), $title);
        if ($existingDocument) {
            return;
        }

        $documentId = DocumentId::generate();
        $filename = $documentId->getValue() . '.docx';

        $dirPath = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $documentId->getValue();
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
            chmod($dirPath, 0777);
        }

        $absolutePath = $dirPath . DIRECTORY_SEPARATOR . $filename;
        try {
            $this->aiRewriter->generateDocxFromHtml($documentHtml, $absolutePath);
        } catch (\Throwable $throwable) {
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }

            if (is_dir($dirPath)) {
                @rmdir($dirPath);
            }


            throw new \RuntimeException('Сбой генерации документа.', 0, $throwable);
        }

        $document = new Document(
            $documentId,
            $title,
            new Amount($amount, new Currency('RUB')),
            new Filename($filename),
            Slug::generate($title, (string)$documentId)->getValue(),
            $category
        );

        $this->documents->add($document);


        $this->flusher->flush();

        $this->flusher->clear();
    }
}
