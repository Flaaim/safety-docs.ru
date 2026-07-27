<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetRelated;

use App\Template\Query\Document\DocumentFetcher;
use App\Template\Query\Document\GetAllByCategory\DocumentDTO;
use App\Template\ReadModel\DocumentFetcherInterface;

final class Handler
{
    public function __construct(
        private readonly DocumentFetcherInterface $documents,
    ) {
    }

    public function handle(Query $query): ListRelatedDocumentsDTO
    {

        $document = $this->documents->getById($query->documentId);

        if (empty($document)) {
            throw new \DomainException('Document not found.');
        }

        $rows = $this->documents->getRelatedDocuments(
            $document['category_id'],
            $document['name'],
            $document['id']
        );

        if (empty($rows)) {
            throw new \DomainException('No related documents found.');
        }

        $relatedDocuments = array_map(
            static fn (array $row) => DocumentDTO::fromArray($row),
            $rows
        );

        return new ListRelatedDocumentsDTO($relatedDocuments);
    }
}
