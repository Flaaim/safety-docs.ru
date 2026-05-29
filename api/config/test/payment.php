<?php

declare(strict_types=1);

use App\Shared\Domain\Service\Payment\PaymentProviderInterface;
use App\Shared\Domain\Service\Payment\PaymentWebhookParserInterface;
use App\Shared\Domain\Service\Payment\Provider\YookassaProvider;
use App\Shared\Domain\Service\Payment\WebhookParser\YookassaWebhookParser;
use Psr\Container\ContainerInterface;
use Test\Functional\Payment\TestPaymentProvider;

return [
    PaymentProviderInterface::class => function (ContainerInterface $container): TestPaymentProvider {
        return $container->get(TestPaymentProvider::class);
    },
    PaymentWebhookParserInterface::class => function (): YookassaWebhookParser {
        return new YookassaWebhookParser();
    }
];
