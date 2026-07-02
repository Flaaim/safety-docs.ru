<?php

declare(strict_types=1);

namespace App\Shared\Domain\Query\GetDocumentForPaymentCreate;

use App\Shared\Domain\Query\DocumentQueryInterface;

final class Handler
{
    public function __construct(
        private readonly DocumentQueryInterface $fetcher,
    ) {}

    public function handle(Query $query): DocumentDTO
    {
        $row = $this->fetcher->getDocumentForPaymentCreate($query->id);

        if(empty($row)){
            throw new \DomainException('Document not found.');
        }

        return DocumentDTO::fromArray($row);
    }
}