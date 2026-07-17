<?php

declare(strict_types=1);

namespace App\ReadModel\Template;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * CQRS Query Handler: loads Template list for admin via DBAL (no ORM aggregates).
 */
final class GetTemplatesHandler
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function handle(GetTemplatesQuery $query): TemplateListResult
    {
        $qb = $this->createBaseQueryBuilder();
        $this->applyFilters($qb, $query);

        $qb->select(
            'd.id',
            'd.name',
            'dir.title AS direction_name',
            'c.title AS category_name',
            'd.created_at',
            "'active' AS status"
        )
            ->orderBy('d.created_at', 'DESC')
            ->setFirstResult(($query->page - 1) * $query->perPage)
            ->setMaxResults($query->perPage);

        $rows = $qb->executeQuery()->fetchAllAssociative();

        $templates = array_map(
            static fn (array $row): TemplateRow => TemplateRow::fromArray($row),
            $rows
        );

        $countQb = $this->createBaseQueryBuilder();
        $this->applyFilters($countQb, $query);
        $countQb->select('COUNT(d.id)');

        $total = (int) $countQb->executeQuery()->fetchOne();
        $totalPages = (int) ceil($total / $query->perPage);
        $totalPages = $totalPages === 0 ? 1 : $totalPages;

        return new TemplateListResult(
            templates: $templates,
            total: $total,
            currentPage: $query->page,
            perPage: $query->perPage,
            totalPages: $totalPages,
        );
    }

    private function createBaseQueryBuilder(): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb
            ->from('documents', 'd')
            ->innerJoin('d', 'categories', 'c', 'c.category_id = d.category_id')
            ->innerJoin('c', 'directions', 'dir', 'dir.id = c.direction_id');
    }

    private function applyFilters(QueryBuilder $qb, GetTemplatesQuery $query): void
    {
        if ($query->directionId !== null && $query->directionId !== '') {
            $qb->andWhere($qb->expr()->eq('c.direction_id', ':directionId'))
                ->setParameter('directionId', $query->directionId);
        }

        if ($query->categoryId !== null && $query->categoryId !== '') {
            $qb->andWhere($qb->expr()->eq('d.category_id', ':categoryId'))
                ->setParameter('categoryId', $query->categoryId);
        }

        if ($query->search !== null && trim($query->search) !== '') {
            $qb->andWhere($qb->expr()->like('d.name', ':search'))
                ->setParameter('search', '%' . trim($query->search) . '%');
        }
    }
}
