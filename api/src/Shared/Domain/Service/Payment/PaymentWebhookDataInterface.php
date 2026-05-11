<?php

namespace App\Shared\Domain\Service\Payment;

interface PaymentWebhookDataInterface
{
    public function getStatus(): string;
    public function isPaid(): bool;
    public function getMetadata(string $key): ?string;
    public function getPaymentId(): string;
    public function getAmount(): float;
    public function getCurrency(): string;
}
