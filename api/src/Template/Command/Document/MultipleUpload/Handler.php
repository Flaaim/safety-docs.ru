<?php

declare(strict_types=1);

namespace App\Template\Command\Document\MultipleUpload;

use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Category\CategoryRepository;
use App\Template\Entity\Document\Amount;
use App\Template\Entity\Document\Document;
use App\Template\Entity\Document\DocumentId;
use App\Template\Entity\Document\DocumentRepository;
use App\Template\Entity\Document\Filename;
use App\Template\Entity\Slug;
use App\Template\Service\File\FileUploaderInterface;
use App\Flusher;
use App\Shared\Domain\ValueObject\Currency;

final class Handler
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly DocumentRepository $documents,
        private readonly Flusher $flusher,
        private readonly FileUploaderInterface $fileUploader
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

        foreach ($command->files as $file) {
            $name = $file->getClientFilename();
            if ($name === null) {
                throw new \DomainException('File name cannot be null.');
            }

            $existing = $this->documents->findByCategoryIdAndName($category->getId(), $name);

            if ($existing !== null) {
                $this->fileUploader->replace(
                    $existing->getId()->getValue(),
                    $existing->getFilename()->getValue(),
                    $file
                );
                $existing->refreshUploadedAt();

                continue;
            }

            $documentId = DocumentId::generate();
            $filename = $this->fileUploader->upload($documentId->getValue(), $file);

            $document = new Document(
                $documentId,
                $name,
                new Amount($command->amount, new Currency('RUB')),
                new Filename($filename),
                Slug::generate($name)->getValue(),
                $category
            );

            $this->documents->add($document);
        }

        $this->flusher->flush();
    }
}
