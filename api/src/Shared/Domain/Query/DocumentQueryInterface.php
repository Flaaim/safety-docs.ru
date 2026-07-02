<?php

namespace App\Shared\Domain\Query;

interface DocumentQueryInterface
{
    public function getDocumentForPaymentCreate(string $id): array;
}
