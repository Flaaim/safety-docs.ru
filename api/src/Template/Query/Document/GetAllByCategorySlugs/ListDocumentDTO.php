<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetAllByCategorySlugs;

use App\Template\Query\Document\GetAllByCategory\DocumentDTO;

final class ListDocumentDTO
{
    public function __construct(
        /** @var DocumentDTO[] */
        public array $items,
        public int $totalCount,
        public int $totalPages,
    ) {
    }
}
