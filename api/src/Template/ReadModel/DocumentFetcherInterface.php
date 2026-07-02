<?php

namespace App\Template\ReadModel;

interface DocumentFetcherInterface
{
    public function getById(string $id): array;
}
