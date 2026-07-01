<?php

declare(strict_types=1);

namespace App\Template\Query\Category;

final class CategoryFetcher
{
    public function __construct(
       private readonly Connection $connection
    ) {}

    public function getAllByDirection(string $directionId): array
    {

    }
}