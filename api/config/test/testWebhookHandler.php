<?php

declare(strict_types=1);

use App\Flusher;
use App\Payment\Command\HookPayment\Handler as HookPaymentHandler;
use App\Payment\Command\HookPayment\SendProduct\Handler;
use App\Payment\Entity\PaymentRepository;
use App\Payment\Service\Delivery\ProductDeliveryService;
use App\Payment\Service\ProductSender;
use App\Product\Entity\ProductRepository;
use App\Shared\Domain\Service\Payment\WebhookParser\YookassaWebhookParser;
use App\Shared\Domain\Service\Template\TemplatePath;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\MailerInterface;
use Test\Functional\Payment\TestPaymentProvider;
use Twig\Environment;

return [
    HookPaymentHandler::class => function(ContainerInterface $c){

        $yookassaWebhookParser = new YookassaWebhookParser();

        $yookassaProvider = $c->get(TestPaymentProvider::class);

        $productSender = new ProductSender(
            $c->get(MailerInterface::class),
            $c->get(TemplatePath::class),
            $c->get(Environment::class),
            $logger = $c->get(LoggerInterface::class),
        );

        $em = $c->get(EntityManagerInterface::class);

        return new HookPaymentHandler(
            $yookassaWebhookParser,
            $yookassaProvider,
            new PaymentRepository($em),
            new Flusher($em),
            new Handler(
                new ProductDeliveryService(
                    new ProductRepository($em),
                    $productSender
                ),
                $c->get(EventDispatcher::class)
            ),
            $logger,
        );
    },
];