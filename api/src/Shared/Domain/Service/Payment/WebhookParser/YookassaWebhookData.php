<?php

namespace App\Shared\Domain\Service\Payment\WebhookParser;

use App\Shared\Domain\Service\Payment\PaymentWebhookDataInterface;

class YookassaWebhookData implements PaymentWebhookDataInterface
{
    public function __construct(
        private readonly string $status,
        private readonly float $amount,
        private readonly array $metadata,
        private readonly string $paymentId,
        private readonly string $currency
    ) {
    }
    public function getStatus(): string
    {
        return $this->status;
    }

    public function isPaid(): bool
    {
        return $this->status === 'succeeded';
    }

    public function getMetadata(string $key): ?string
    {
        return $this->metadata[$key] ?? null;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
