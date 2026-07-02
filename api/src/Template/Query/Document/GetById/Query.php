<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetById;

final class Query
{
    public function __construct(
       public readonly string $id,
    ) {}
}