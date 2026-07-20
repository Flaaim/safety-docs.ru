<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetChildrenCategories;

use App\Template\Query\Category\CategoryFetcher;

final class Handler
{
    public function __construct(
        private readonly CategoryFetcher $fetcher
    ) {
    }

    public function handle(): array
    {
        $rows = $this->fetcher->getAllChildrenCategories();

        return array_map(
            static fn (array $row) => CategoryDTO::fromArray($row),
            $rows
        );
    }
}
