<?php

namespace App\Shared\Domain\Query;

interface DocumentQueryInterface
{
    public function getDocument(string $id): DocumentQueryDTO;
}
