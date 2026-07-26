<?php

declare(strict_types=1);

namespace App\Parser\Entity;

final class DocumentItem
{
    public function __construct(
        public string $title,
        public string $urlPath,
    ) {
    }
}
