<?php

declare(strict_types=1);

namespace App\Sitemap\Query\GetData;

final class SitemapDocumentDTO
{
    public function __construct(
        public string $directionSlug,
        public string $categorySlug,
        public string $documentSlug,
        public string $createdAt
    ) {
    }


    public static function fromArray(array $data): self
    {
        return new self(
            directionSlug: $data['direction_slug'],
            categorySlug: $data['category_slug'],
            documentSlug: $data['document_slug'],
            createdAt: $data['created_at'],
        );
    }
}
