<?php

namespace App\Shared;

interface AggregateRoot
{
    public function releaseEvents(): array;

}