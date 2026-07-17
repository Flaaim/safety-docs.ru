<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetBySlugs;

use App\Template\Query\Category\GetBySlug\CategoryDTO;
use App\Template\ReadModel\CategoryFetcherInterface;
use App\Template\ReadModel\DirectionFetcherInterface;

final class Handler
{
    public function __construct(
        private readonly DirectionFetcherInterface $directions,
        private readonly CategoryFetcherInterface $categories,
    ) {
    }

    public function handle(Query $query): CategoryDTO
    {
        $direction = $this->directions->getBySlug($query->directionSlug);

        if ($direction === []) {
            throw new \DomainException('Direction does not exist.');
        }

        $row = $this->categories->getBySlugAndDirectionId(
            $query->categorySlug,
            $direction['id']
        );

        if ($row === []) {
            throw new \DomainException('Category not found.');
        }

        return CategoryDTO::fromArray($row);
    }
}
