<?php

declare(strict_types=1);

namespace App\Shared\Domain\Query\GetDocumentForPaymentCreate;

final class Query
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
