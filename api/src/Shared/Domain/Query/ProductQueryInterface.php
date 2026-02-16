<?php

namespace App\Shared\Domain\Query;

interface ProductQueryInterface
{
    public function getProduct(string $id): ProductQueryDTO;
}