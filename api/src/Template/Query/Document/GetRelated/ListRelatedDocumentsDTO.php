<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetRelated;

use App\Template\Query\Document\GetAllByCategory\DocumentDTO;

final class ListRelatedDocumentsDTO
{
    public function __construct(
        /** @var DocumentDTO[] */
        public array $items,
    ) {
    }
}
