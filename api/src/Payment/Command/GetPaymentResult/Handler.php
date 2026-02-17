<?php

namespace App\Payment\Command\GetPaymentResult;

use App\Payment\Entity\DTO\PaymentResultDTO;
use App\Payment\Entity\PaymentRepository;

class Handler
{
    public function __construct(
        private readonly PaymentRepository $payments
    )
    {}

    public function handle(Command $command): PaymentResultDTO
    {

        $payment = $this->payments->getByToken($command->returnToken);

        $payment->validateToken($command->returnToken, new \DateTimeImmutable('now'));

        return new PaymentResultDTO(
            $payment->getReturnToken()->getValue(),
            $payment->getStatus()->getValue(),
            $payment->getEmail()->getValue()
        );
    }
}