<?php

declare(strict_types=1);

namespace App\Template\Query\Direction\GetBySlug;

final class DirectionDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $text,
        public string $slug,
        public array $categories,
    ) {
    }
}
