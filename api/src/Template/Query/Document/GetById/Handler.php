<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetById;

use App\Template\ReadModel\DocumentFetcherInterface;

final class Handler
{
    public function __construct(
        private readonly DocumentFetcherInterface $fetcher
    ) {
    }

    public function handle(Query $query): DocumentDTO
    {
        $row = $this->fetcher->getById($query->id);

        if (empty($row)) {
            throw new \DomainException('Document not found.');
        }

        return DocumentDTO::fromArray($row);
    }
}
