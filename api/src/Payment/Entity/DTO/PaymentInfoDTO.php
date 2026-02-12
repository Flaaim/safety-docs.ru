<?php

namespace App\Payment\Entity\DTO;

class PaymentInfoDTO
{
    public function __construct(
        public string  $paymentId,
        public string  $status,
        public ?string $redirectUrl = null,
        public ?array  $paymentData = null,
    ) {}
}