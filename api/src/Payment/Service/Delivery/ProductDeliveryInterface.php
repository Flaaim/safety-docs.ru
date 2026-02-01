<?php

namespace App\Payment\Service\Delivery;

use App\Shared\Domain\Service\Payment\PaymentWebhookDataInterface;

interface ProductDeliveryInterface
{
    public function deliver(PaymentWebhookDataInterface $paymentWebHookData): void;
}