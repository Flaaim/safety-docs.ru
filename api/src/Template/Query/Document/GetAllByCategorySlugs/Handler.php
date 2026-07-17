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
     * @return list<DocumentDTO>
     */
    public function handle(Query $query): array
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

        $rows = $this->documents->getAllByCategory($category['category_id']);

        return array_map(
            static fn (array $row) => DocumentDTO::fromArray($row),
            $rows
        );
    }
}
