<?php

namespace App\Product\Command\Upsert;

class Response
{
    public function __construct(public string $productId)
    {}
}