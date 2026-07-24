<?php

declare(strict_types=1);

namespace App\Template\Query\Document\Preview;

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Template\Query\Document\GetBySlug\DocumentDTO;
use App\Template\ReadModel\DocumentFetcherInterface;
use App\Template\Service\DocumentPreviewer;

final class Handler
{
    public function __construct(
        private readonly DocumentPreviewer $documentPreviewer,
        private readonly DocumentFetcherInterface $documents,
        private readonly FileSystemPath $fileSystemPath,
    ) {
    }


    public function handle(Query $query): string
    {
        $row = $this->documents->getById($query->documentId);

        if (empty($row)) {
            throw new \DomainException('Document not found.');
        }

        $document = DocumentDTO::fromArray($row);

        $directory = pathinfo($document->filename, PATHINFO_FILENAME);

        $absolutePathToDocument = $this->fileSystemPath->getValue() .
            DIRECTORY_SEPARATOR .
            $directory .
            DIRECTORY_SEPARATOR .
            $document->filename;

        return $this->documentPreviewer->getHtml($absolutePathToDocument);
    }
}
