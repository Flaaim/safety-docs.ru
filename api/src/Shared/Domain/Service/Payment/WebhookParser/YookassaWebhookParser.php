<?php

namespace App\Shared\Domain\Service\Payment\WebhookParser;

use App\Shared\Domain\Service\Payment\PaymentWebhookParserInterface;


class YookassaWebhookParser implements PaymentWebhookParserInterface
{
    const PROVIDER_NAME = 'Yookassa';
    const NOTIFICATION_TYPE = 'notification';

    public function supports(string $provider, array $data): bool
    {
        if ($provider !== self::PROVIDER_NAME) {
            return false;
        }

        return isset($data['object']['id'])
            && isset($data['type'])
            && $data['type'] === self::NOTIFICATION_TYPE;

    }
}
