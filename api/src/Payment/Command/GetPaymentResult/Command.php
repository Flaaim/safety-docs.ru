<?php

namespace App\Payment\Command\GetPaymentResult;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid(message: 'Token is not correct.')]
        public string $returnToken
    ) {
    }
}
