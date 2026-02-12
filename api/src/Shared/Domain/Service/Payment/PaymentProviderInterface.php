<?php

namespace App\Shared\Domain\Service\Payment;



use App\Payment\Entity\DTO\MakePaymentDTO;
use App\Payment\Entity\DTO\PaymentCallbackDTO;
use App\Payment\Entity\DTO\PaymentInfoDTO;


interface PaymentProviderInterface
{
    public function initiatePayment(MakePaymentDTO $paymentData): PaymentInfoDTO;

    public function handleCallback(PaymentCallbackDTO $callbackData): ?string;

    public function checkPaymentStatus(string $paymentId): string;

    public function getSupportedCurrencies(): array;

    public function getName(): string;
}
