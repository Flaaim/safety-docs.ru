<?php

namespace App\Payment\Command\HookPayment\SendProduct;

use App\Payment\Entity\Payment;
use App\Shared\Domain\Service\Payment\PaymentWebhookDataInterface;

class Command
{
    public function __construct(
        public readonly Payment $payment,
        public readonly PaymentWebhookDataInterface $paymentWebHookData
    ){}
}