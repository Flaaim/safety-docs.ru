<?php

declare(strict_types=1);

namespace App\Distribution\Command\DraftNewsletter;

use Symfony\Component\Validator\Constraints as Assert;
final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public string $subject,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $templateId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $projectId
    ) {}
}