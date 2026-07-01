<?php

declare(strict_types=1);

namespace App\Template\Query\Category;

use App\Template\ReadModel\CategoryFetcherInterface;
use Doctrine\DBAL\Connection;

final class CategoryFetcher implements CategoryFetcherInterface
{
    public function __construct(
       private readonly Connection $connection
    ) {}

    public function getAllByDirection(string $directionId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('*')
            ->from('category', 'c')
            ->where('c.directionId = :directionId')
            ->setParameter('directionId', $directionId);

        $result = $qb->executeQuery();

        return $result->fetchAllAssociative();
    }
}