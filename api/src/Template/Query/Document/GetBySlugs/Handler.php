<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetBySlugs;

use App\Template\Query\Document\GetBySlug\DocumentDTO;
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

    public function handle(Query $query): DocumentDTO
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

        $row = $this->documents->getBySlugAndCategoryId(
            $query->templateSlug,
            $category['category_id']
        );

        if ($row === []) {
            throw new \DomainException('Document not found.');
        }

        return DocumentDTO::fromArray($row);
    }
}
