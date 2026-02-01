<?php

namespace App\Shared\Domain\Service\Payment\WebhookParser;

use App\Shared\Domain\Service\Payment\PaymentWebhookDataInterface;
use App\Shared\Domain\Service\Payment\PaymentWebhookParserInterface;
use InvalidArgumentException;


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

    public function parse(array $data): PaymentWebhookDataInterface
    {
        if (!$this->supports(self::PROVIDER_NAME, $data)) {
            throw new InvalidArgumentException("Invalid Yookassa webhook format");
        }

        $object = $data['object'];

        $amount = $object['amount']['value'] ?? '0';
        $currency = $object['amount']['currency'] ?? 'RUB';

        return new YookassaWebhookData(
            $object['status'],
            $amount,
            $object['metadata'] ?? [],
            $object['id'],
            $currency
        );
    }
}
