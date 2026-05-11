<?php

namespace App\Shared\Domain\Service\Notification;

interface NotificationInterface
{
    public function notify(object $event): void;
}
