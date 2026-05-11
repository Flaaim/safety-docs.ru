<?php

namespace App\Payment\Command\CreatePayment;

class Response implements \JsonSerializable
{
    public function __construct(
        public float $amount,
        public string $currency,
        public string $status,
        public string $returnUrl,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'returnUrl' => $this->returnUrl,
        ];
    }
}
