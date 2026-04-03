<?php

namespace App\Product\Command\Update;

interface UpdateProductHandlerInterface
{
    public function getType(string $type): bool;
}