<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetAllByCategorySlugs;

use App\Template\Query\Document\GetAllByCategory\DocumentDTO;
use App\Template\ReadModel\CategoryFetcherInterface;
use App\Template\ReadModel\DirectionFetcherInterface;
use App\Template\ReadModel\DocumentFetcherInterface;

final class Handler
{
    public function __construct(
        private readonly DirectionFetcherInterface $directions,
        private readonly CategoryFetcherInterface $categories,
        private readonly DocumentFetcherInterface $documents,
    ) {
    }

    /**
     * @param Query $query
     * @return ListDocumentDTO
     */
    public function handle(Query $query): ListDocumentDTO
    {
        $direction = $this->directions->getBySlug($query->directionSlug);

        if ($direction === []) {
            throw new \DomainException('Direction does not exist.');
        }

        $category = $this->categories->getBySlugAndDirectionId(
            $query->categorySlug,
            $direction['id']
        );

        if ($category === []) {
            throw new \DomainException('Category not found.');
        }

        $result = $this->documents->getPaginatedByCategory(
            $category['category_id'],
            $query->page,
            $query->limit,
            $query->search
        );

        $items = array_map(
            static fn (array $row) => DocumentDTO::fromArray($row),
            $result['items']
        );

        $totalCount = $result['totalCount'];

        $totalPages = $totalCount > 0 ? (int) ceil($totalCount / $query->limit) : 0;

        return new ListDocumentDTO(
            items: $items,
            totalCount: $totalCount,
            totalPages: $totalPages
        );
    }
}
