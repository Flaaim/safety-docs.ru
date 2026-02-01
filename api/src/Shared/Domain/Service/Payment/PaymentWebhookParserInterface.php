<?php

namespace App\Shared\Domain\Service\Payment;

interface PaymentWebhookParserInterface
{
    public function supports(string $provider, array $data): bool;
    public function parse(array $data): PaymentWebhookDataInterface;
}
