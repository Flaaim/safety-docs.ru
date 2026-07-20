<?php

declare(strict_types=1);

namespace App\Parser\Event;

final class ProcessedSingleDocument
{
    public function __construct(
        public readonly string $categoryId,
        public readonly float $amount,
        public readonly string $title,
        public readonly string $href,
        public readonly string $cookie
    ) {
    }
}
