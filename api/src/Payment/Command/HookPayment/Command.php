<?php

namespace App\Payment\Command\HookPayment;

class Command
{
    public function __construct(
        public readonly array $data
    ) {
    }
}
