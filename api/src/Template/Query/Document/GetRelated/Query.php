<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetRelated;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $documentId,
    ) {
    }
}
