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

        $qb->select('c.category_id, c.title, c.description, c.text, c.slug, c.parent_id, c.direction_id')
            ->from('categories', 'c')
            ->where('c.direction_id = :directionId')
            ->setParameter('directionId', $directionId)
            ->orderBy('c.parent_id', 'ASC')
            ->addOrderBy('c.category_id', 'ASC');

        $result = $qb->executeQuery();

        return $this->buildTree($result->fetchAllAssociative());
    }

    private function buildTree(array $categories): array
    {
        $tree = [];
        $references = [];

        foreach ($categories as &$category) {
            $category['children'] = [];
            $references[$category['category_id']] = &$category;
        }

        foreach ($categories as &$category) {
            if ($category['parent_id'] === null) {
                $tree[] = &$category;
            } else {
                if (isset($references[$category['parent_id']])) {
                    $references[$category['parent_id']]['children'][] = &$category;
                }
            }
        }

        return $tree;
    }
}