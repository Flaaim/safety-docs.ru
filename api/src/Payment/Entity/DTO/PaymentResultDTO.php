<?php

namespace App\Payment\Entity\DTO;

class PaymentResultDTO
{
    public function __construct(
        public string $returnToken,
        public string $status,
        public string $email
    )
    {}
}