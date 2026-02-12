<?php

namespace Test\Functional\Payment;

use App\Payment\Entity\DTO\MakePaymentDTO;
use App\Payment\Entity\DTO\PaymentCallbackDTO;
use App\Payment\Entity\DTO\PaymentInfoDTO;
use App\Shared\Domain\Service\Payment\PaymentProviderInterface;

class TestPaymentProvider implements PaymentProviderInterface
{
    public function initiatePayment(MakePaymentDTO $paymentData): PaymentInfoDTO
    {
        // TODO: Implement checkPaymentStatus() method.
    }

    public function handleCallback(PaymentCallbackDTO $callbackData): ?string
    {
        return 'hook_test_payment_id';
    }

    public function checkPaymentStatus(string $paymentId): string
    {
        // TODO: Implement checkPaymentStatus() method.
    }

    public function getSupportedCurrencies(): array
    {
        // TODO: Implement getSupportedCurrencies() method.
    }

    public function getName(): string
    {
        return 'Yookassa';
    }
}