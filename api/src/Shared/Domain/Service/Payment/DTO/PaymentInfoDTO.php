<?php

namespace App\Shared\Domain\Service\Payment\DTO;

class PaymentInfoDTO
{
    public function __construct(
        public string  $paymentId,
        public string  $status,
        public ?string $redirectUrl = null, // Для перенаправления на шлюз
        public ?array  $paymentData = null, // Дополнительные данные провайдера
    ) {}
}
