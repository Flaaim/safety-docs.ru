<?php

declare(strict_types=1);

namespace App\Sitemap\Query;

use App\Sitemap\ReadModel\SitemapFetcherInterface;
use Doctrine\DBAL\Connection;

final class SitemapFetcher implements SitemapFetcherInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }
    public function getSitemapData(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(
            'dir.slug AS direction_slug',
            'c.slug AS category_slug',
            'd.slug AS document_slug',
            'd.created_at'
        )
            ->from('documents', 'd')
            ->innerJoin('d', 'categories', 'c', 'd.category_id = c.category_id')
            ->innerJoin('c', 'directions', 'dir', 'c.direction_id = dir.id')
            ->orderBy('d.id', 'DESC');

        $result = $qb->executeQuery();

        return $result->fetchAllAssociative();
    }
}
