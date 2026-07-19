<?php

declare(strict_types=1);

namespace App\Parser\Command\Launch;

final class Command
{
    public function __construct(
        public readonly string $url,
        public readonly string $cookie
    ){
    }
}