<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetAllByDirection;

final class CategoryDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $text,
        public string $slug
    ) {}
}