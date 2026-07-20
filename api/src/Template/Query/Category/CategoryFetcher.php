<?php

declare(strict_types=1);

namespace App\Template\Query\Category;

use App\Template\ReadModel\CategoryFetcherInterface;
use Doctrine\DBAL\Connection;

final class CategoryFetcher implements CategoryFetcherInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

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

    public function getBySlugAndDirectionId(string $slug, string $directionId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('p.category_id, p.title, p.description, p.text, p.slug, p.parent_id, p.direction_id, 
            c.category_id as child_id, 
            c.title as child_title, 
            c.description as child_description, 
            c.text as child_text, 
            c.slug as child_slug, 
            c.parent_id as child_parent_id')
            ->from('categories', 'p')
            ->leftJoin('p', 'categories', 'c', 'c.parent_id = p.category_id')
            ->where('p.slug = :slug')
            ->andWhere('p.direction_id = :directionId')
            ->setParameter('slug', $slug)
            ->setParameter('directionId', $directionId);

        $result = $qb->executeQuery();

        $rows = $result->fetchAllAssociative();

        $data = [];
        foreach ($rows as $row) {
            if (empty($data)) {
                $data = [
                    'category_id' => $row['category_id'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'text' => $row['text'],
                    'slug' => $row['slug'],
                    'parent_id' => $row['parent_id'],
                    'direction_id' => $row['direction_id'],
                    'children' => []
                ];
            }

            if ($row['child_id'] !== null) {
                $data['children'][] = [
                    'category_id' => $row['child_id'],
                    'title' => $row['child_title'],
                    'description' => $row['child_description'],
                    'text' => $row['child_text'],
                    'slug' => $row['child_slug'],
                    'parent_id' => $row['child_parent_id'],
                    'direction_id' => $row['direction_id'],
                    'children' => []
                ];
            }
        }

        return $data;
    }
    public function getAllChildrenCategories(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('c.category_id, c.title, c.description, c.text, c.slug, c.parent_id')
            ->from('categories', 'c')
            ->leftJoin('c', 'categories', 'sub', 'sub.parent_id = c.category_id')
            ->where($qb->expr()->isNotNull('c.parent_id'))
            ->orWhere($qb->expr()->isNull('sub.category_id'));

        $result = $qb->executeQuery();

        return $result->fetchAllAssociative();
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
