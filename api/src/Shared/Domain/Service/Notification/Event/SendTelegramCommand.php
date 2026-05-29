<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service\Notification\Event;

final class SendTelegramCommand
{
    public function __construct(
        public string $email,
        public string $price,
        public string $productName,
    ) {
    }
}
