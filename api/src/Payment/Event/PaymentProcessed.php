<?php

declare(strict_types=1);

namespace App\Payment\Event;

final class PaymentProcessed
{
    public function __construct(
        public string $productId,
        public string $email,
    ) {}
}