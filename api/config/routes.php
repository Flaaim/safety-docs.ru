<?php

declare(strict_types=1);

use App\Http\Action\V1\Auth\GetToken\RequestAction;
use App\Http\Action\V1\Payment;
use App\Http\Action\V1\Product;
use App\Http\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function(App $app): void {

    $app->group('/v1', function (RouteCollectorProxy $group): void {

        $group->group('/payments', function (RouteCollectorProxy $group): void {
            $group->post('/process-payment', Payment\CreatePayment\RequestAction::class);
            $group->post('/payment-webhook', Payment\HookPayment\RequestAction::class);

            $group->post('/result', Payment\Result\RequestAction::class);
        });


        $group->group('/products', function (RouteCollectorProxy $group): void {
            $group->get('/', Product\GetAll\RequestAction::class)->add(AuthMiddleware::class);

            $group->post('/upsert', Product\Upsert\RequestAction::class)->add(AuthMiddleware::class);
            $group->post('/upload', Product\Upload\RequestAction::class)->add(AuthMiddleware::class);

            $group->get('/get/{id:[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}}', \App\Http\Action\V1\Product\Get\RequestAction::class);
            $group->get('/get/{slug:[a-z-]+}', Product\GetBySlug\RequestAction::class);
        });

        $group->group('/auth', function (RouteCollectorProxy $group): void {
            $group->post('/login', RequestAction::class);
        });
    });



};