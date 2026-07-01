<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetAllByDirection;

final class Query
{
    public function __construct(
        public string $directionId
    ) {}
}