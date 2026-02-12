<?php

namespace App\Payment\Entity\DTO;

class PaymentCallbackDTO
{
    public function __construct(
        public array  $rawData,
        public string $signature,
        public string $provider,
    ) {}
}
