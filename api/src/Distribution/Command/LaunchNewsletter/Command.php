<?php

declare(strict_types=1);

namespace App\Distribution\Command\LaunchNewsletter;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $newsletterId,
    ) {
    }
}
