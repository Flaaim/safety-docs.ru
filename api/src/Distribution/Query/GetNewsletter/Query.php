<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetNewsletter;

final class Query
{
    public function __construct(
        public string $newsletterId,
    ) {
    }
}
