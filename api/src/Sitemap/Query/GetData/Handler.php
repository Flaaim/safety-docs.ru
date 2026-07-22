<?php

declare(strict_types=1);

namespace App\Sitemap\Query\GetData;

use App\Sitemap\Query\SitemapFetcher;

final class Handler
{
    public function __construct(
        private readonly SitemapFetcher $fetcher,
    ) {
    }

    public function handle(): array
    {
        $rows = $this->fetcher->getSitemapData();

        return array_map(
            static fn($row) => SitemapDocumentDTO::fromArray($row),
            $rows
        );
    }
}
