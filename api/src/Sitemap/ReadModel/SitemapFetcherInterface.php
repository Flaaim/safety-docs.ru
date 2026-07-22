<?php

namespace App\Sitemap\ReadModel;

interface SitemapFetcherInterface
{
    public function getSitemapData(): array;
}
