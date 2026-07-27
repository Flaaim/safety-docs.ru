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
    ) {
    }
    public function getById(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('d.id, d.name, d.amount, d.filename, d.created_at, d.slug, d.category_id')
            ->from('documents', 'd')
            ->where($qb->expr()->eq('d.id', ':id'))
            ->setParameter('id', $id);

        $result = $qb->executeQuery();

        $row = $result->fetchAssociative();

        if (!$row) {
            return [];
        }
        return $this->normalizeRow($row);
    }

    public function getPaginatedByCategory(string $categoryId, int $page = 1, int $limit = 15, ?string $search = null): array
    {
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

            $qb->select('d.id, d.name, d.amount, d.filename, d.created_at, d.slug')
            ->from('documents', 'd')
            ->where($qb->expr()->eq('d.category_id', ':categoryId'))
            ->setParameter('categoryId', $categoryId);

        if ($search !== null && trim($search) !== '') {
            $qb->andWhere($qb->expr()->or(
                $qb->expr()->like('LOWER(d.name)', ':search'),
                $qb->expr()->like('LOWER(d.filename)', ':search')
            ))
                ->setParameter('search', '%' . mb_strtolower(trim($search)) . '%');
        }

        $countQb = clone $qb;
        $totalCount = (int) $countQb->select('COUNT(d.id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('d.id, d.name, d.amount, d.filename, d.created_at, d.slug')
            ->orderBy('d.name', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();

        $qb->select('d.id, d.name, d.amount, d.filename, d.created_at, d.slug')
            ->from('documents', 'd')
            ->where($qb->expr()->eq('d.category_id', ':categoryId'))
            ->setParameter('categoryId', $categoryId)
            ->orderBy('d.name', 'ASC');

        $items = array_map(fn (array $row) => $this->normalizeRow($row), $rows);

        return [
            'items' => $items,
            'totalCount' => $totalCount,
        ];
    }

    public function getBySlugAndCategoryId(string $slug, string $categoryId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('d.id, d.name, d.amount, d.filename, d.created_at, d.slug')
            ->from('documents', 'd')
            ->where($qb->expr()->eq('d.slug', ':slug'))
            ->andWhere($qb->expr()->eq('d.category_id', ':categoryId'))
            ->setParameter('slug', $slug)
            ->setParameter('categoryId', $categoryId);

        $row = $qb->executeQuery()->fetchAssociative();

        if (!$row) {
            return [];
        }

        return $this->normalizeRow($row);
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

        if (!$row) {
            return [];
        }
        return $row;
    }

    public function getRelatedDocuments(string $categoryId, string $documentName, string $documentId): array
    {
        $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower(trim($documentName)));
        if ($cleanName === null) {
            return [];
        }
        $words = preg_split('/\s+/u', $cleanName, -1, PREG_SPLIT_NO_EMPTY);
        if ($words === false) {
            return [];
        }
        // 2. Отбрасываем предлоги и короткие слова (оставляем > 3 символов)
        $significantWords = array_filter($words, fn($w) => mb_strlen($w) > 3);

        // 3. Берем первые 5 значимых слов
        $searchWords = array_slice(array_values($significantWords), 0, 5);

        if (empty($searchWords)) {
            return [];
        }

        $qb = $this->connection->createQueryBuilder();

        $qb->select('d.id', 'd.name', 'd.amount', 'd.filename', 'd.created_at', 'd.slug')
            ->from('documents', 'd')
            ->where($qb->expr()->eq('d.category_id', ':categoryId'))
            ->andWhere($qb->expr()->neq('d.id', ':documentId'))
            ->setParameter('categoryId', $categoryId)
            ->setParameter('documentId', $documentId);

        $orConditions = [];
        $relevanceParts = [];

        foreach ($searchWords as $index => $word) {
            $paramName = "word_$index";

            // Добавляем условие в массив вместо orX()->add()
            $orConditions[] = $qb->expr()->like('LOWER(d.name)', ":$paramName");

            // Начисление баллов: если слово найдено, даем 1 балл
            $relevanceParts[] = "(CASE WHEN LOWER(d.name) LIKE :$paramName THEN 1 ELSE 0 END)";

            $qb->setParameter($paramName, '%' . $word . '%');
        }

        $qb->andWhere($qb->expr()->or(...$orConditions));

        $relevanceSql = implode(' + ', $relevanceParts);
        $qb->addSelect("($relevanceSql) AS relevance");

        $qb->orderBy('relevance', 'DESC')
            ->addOrderBy('d.created_at', 'DESC')
            ->setMaxResults(5);

        $result = $qb->executeQuery();
        $rows = $result->fetchAllAssociative();

        if (!$rows) {
            return [];
        }

        return array_map(fn (array $row) => $this->normalizeRow($row), $rows);
    }
    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeRow(array $row): array
    {
        if (isset($row['created_at']) && !isset($row['createdAt'])) {
            $row['createdAt'] = $row['created_at'];
            unset($row['created_at']);
        }

        if (isset($row['amount'])) {
            $row['amount'] = (float) $row['amount'];
        }

        if (isset($row['relevance'])) {
            unset($row['relevance']);
        }

        return $row;
    }
}
