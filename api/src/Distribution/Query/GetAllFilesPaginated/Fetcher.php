<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetAllFilesPaginated;

use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function fetch(Query $query): FilePaginatedCollection
    {
        $sortDir = strtoupper($query->sortDir) === 'ASC' ? 'ASC' : 'DESC';

        $qb = $this->connection->createQueryBuilder()
            ->select('p.*')
            ->from('distribution_contacts_files', 'p')
            ->orderBy('p.' . $query->sortBy, $sortDir)
            ->setFirstResult(($query->page - 1) * $query->perPage)
            ->setMaxResults($query->perPage);

        $data = $qb->fetchAllAssociative();

        $countQb = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('distribution_contacts_files', 'p');

        $total = (int) $countQb->fetchOne();
        $totalPages = (int) ceil($total / $query->perPage);
        $totalPages = $totalPages === 0 ? 1 : $totalPages;

        return new FilePaginatedCollection(
            files: $data,
            total: $total,
            currentPage: $query->page,
            perPage: $query->perPage,
            totalPages: $totalPages
        );
    }
}
