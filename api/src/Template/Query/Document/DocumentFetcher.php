<?php

declare(strict_types=1);

namespace App\Template\Query\Document;

use App\Shared\Domain\Query\DocumentQueryInterface;
use App\Template\ReadModel\DocumentFetcherInterface;
use Doctrine\DBAL\Connection;

final class DocumentFetcher implements DocumentFetcherInterface, DocumentQueryInterface
{
    public function __construct(
       private readonly Connection $connection,
    ) {}
    public function getById(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('d.id, d.name, d.amount, d.filename, d.created_at, d.slug')
            ->from('documents', 'd')
            ->where($qb->expr()->eq('d.id', ':id'))
            ->setParameter('id', $id);

        $result = $qb->executeQuery();

        $row = $qb->fetchAssociative();

        if(!$row) {
            return [];
        }
        return $row;
    }

    public function getDocumentForPaymentCreate(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('d.id, d.amount')
            ->from('documents', 'd')
            ->where($qb->expr()->eq('d.id', ':id'))
            ->setParameter('id', $id);

        $result = $qb->executeQuery();

        $row = $result->fetchAssociative();

        if(!$row) {
            return [];
        }
        return $row;
    }
}