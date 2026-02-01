<?php

namespace App\Shared\Domain\Event\Payment;

use App\Payment\Entity\Payment;
use Symfony\Contracts\EventDispatcher\Event;

class SuccessfulPaymentEvent extends Event
{
    public function __construct(private readonly Payment $payment)
    {}

    public function getPayment(): Payment
    {
        return $this->payment;
    }
}