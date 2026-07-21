<?php

declare(strict_types=1);

namespace App\Parser\Entity;

final class DocumentAttachment
{
    public function __construct(
        public readonly string $url,
        public readonly string $extension = 'docx',
    ) {
    }
}
