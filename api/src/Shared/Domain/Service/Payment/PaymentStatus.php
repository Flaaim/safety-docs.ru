<?php

namespace App\Shared\Domain\Service\Payment;

interface PaymentStatus
{
    public const PENDING = 'pending';
    public const SUCCEEDED = 'succeeded';
    public const FAILED = 'failed';
    public const CANCELED = 'canceled';
}
