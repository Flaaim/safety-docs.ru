<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetAllNewslettersPaginated;

use App\Distribution\Entity\Newsletter\NewsletterStatus;
use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function fetch(Query $query): NewsletterPaginatedCollection
    {
        $sortDir = strtoupper($query->sortDir) === 'ASC' ? 'ASC' : 'DESC';

        $qb = $this->connection->createQueryBuilder();
        $qb->select('n.*')
            ->from('newsletters', 'n')
            ->leftJoin('n', 'distribution_projects', 'dp', 'n.project_id = dp.id')
            ->orderBy('n.' . $query->sortBy, $sortDir)
            ->setFirstResult(($query->page - 1) * $query->perPage)
            ->setMaxResults($query->perPage);

        if ($query->archived !== true) {
            $qb->Andwhere($qb->expr()->neq('n.status', ':status'))
                ->setParameter('status', NewsletterStatus::Archived->value);
        }

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
