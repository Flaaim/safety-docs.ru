<?php

declare(strict_types=1);

namespace App\Template\Query\Direction;

use Doctrine\DBAL\Connection;

final class DirectionFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function getBySlug(string $slug): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('
        d.id, 
        d.title, 
        d.description, 
        d.text, 
        d.slug, 
        c.category_id as categoryId, 
        c.title as categoryTitle,
        c.description as categoryDescription,
        c.slug as categorySlug
        ')
            ->from('directions', 'd')
            ->leftJoin('d', 'categories', 'c', 'd.id = c.direction_id')
            ->where('d.slug = :slug')
            ->setParameter('slug', $slug);

        $result = $qb->executeQuery();

        $rows = $result->fetchAllAssociative();

        if (!$rows) {
            throw new \DomainException('Direction does not exist.');
        }

        $data = [];
        foreach ($rows as $row) {
            $directionId = $row['id'];

            if (!isset($data[$directionId])) {
                $data[$directionId] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'text' => $row['text'],
                    'slug' => $row['slug'],
                    'categories' => []
                ];
            }

            if ($row['categoryId'] !== null) {
                $data[$directionId]['categories'][] = [
                    'id' => $row['categoryId'],
                    'title' => $row['categoryTitle'],
                    'description' => $row['categoryDescription'],
                    'slug' => $row['categorySlug'],
                ];
            }
        }

        return array_values($data);
    }
}
