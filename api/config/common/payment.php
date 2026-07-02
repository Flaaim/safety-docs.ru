<?php

declare(strict_types=1);

use App\Shared\Domain\Query\DocumentQueryInterface;
use App\Shared\Domain\Service\Payment\PaymentProviderInterface;
use App\Shared\Domain\Service\Payment\PaymentWebhookParserInterface;
use App\Shared\Domain\Service\Payment\Provider\YookassaProvider;
use App\Shared\Domain\Service\Payment\WebhookParser\YookassaWebhookParser;
use App\Template\Query\Document\DocumentFetcher;
use Psr\Container\ContainerInterface;

return [
    PaymentProviderInterface::class => function (ContainerInterface $container): YookassaProvider {
        return $container->get(YookassaProvider::class);
    },
    PaymentWebhookParserInterface::class => function (): YookassaWebhookParser {
        return new YookassaWebhookParser();
    },
    DocumentQueryInterface::class => DI\get(DocumentFetcher::class),
];
