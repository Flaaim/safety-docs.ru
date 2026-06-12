<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetAllNewslettersPaginated;

use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function fetch(Query $query): NewsletterPaginatedCollection
    {
        $sortDir = strtoupper($query->sortDir) === 'ASC' ? 'ASC' : 'DESC';

        $qb = $this->connection->createQueryBuilder()
            ->select('n.*')
            ->from('newsletters', 'n')
            ->leftJoin('n', 'distribution_projects', 'dp', 'n.project_id = dp.id')
            ->orderBy('n.' . $query->sortBy, $sortDir)
            ->setFirstResult(($query->page - 1) * $query->perPage)
            ->setMaxResults($query->perPage);

        $data = $qb->fetchAllAssociative();

        $countQb = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('newsletters', 'n');

        $total = (int) $countQb->fetchOne();
        $totalPages = (int) ceil($total / $query->perPage);
        $totalPages = $totalPages === 0 ? 1 : $totalPages;

        return new NewsletterPaginatedCollection(
            newsletters: $data,
            total: $total,
            currentPage: $query->page,
            perPage: $query->perPage,
            totalPages: $totalPages
        );
    }
}
