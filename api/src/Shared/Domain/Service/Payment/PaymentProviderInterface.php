<?php

namespace App\Shared\Domain\Service\Payment;



use App\Shared\Domain\Service\Payment\DTO\MakePaymentDTO;
use App\Shared\Domain\Service\Payment\DTO\PaymentCallbackDTO;
use App\Shared\Domain\Service\Payment\DTO\PaymentInfoDTO;

interface PaymentProviderInterface
{
    public function initiatePayment(MakePaymentDTO $paymentData): PaymentInfoDTO;

    public function handleCallback(PaymentCallbackDTO $callbackData): ?string;

    public function checkPaymentStatus(string $paymentId): string;

    public function getSupportedCurrencies(): array;

    public function getName(): string;
}
