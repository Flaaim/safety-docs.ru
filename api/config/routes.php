<?php

declare(strict_types=1);

use App\Http\Action\V1\Auth\GetToken\RequestAction;
use App\Http\Action\V1\Direction;
use App\Http\Action\V1\Payment;
use App\Http\Action\V1\Product;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\UploadFileHandler;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function(App $app): void {

    $app->group('/v1', function (RouteCollectorProxy $group): void {


        $group->group('/payments', function (RouteCollectorProxy $group): void {

            $group->post('/process-payment', Payment\CreatePayment\RequestAction::class);
            $group->post('/payment-webhook', Payment\HookPayment\RequestAction::class);

            $group->get('/get/{token}', Payment\GetPaymentResult\RequestAction::class);

        });

        $group->group('/products', function (RouteCollectorProxy $group): void {
            $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

            $group->post('', Product\Add\RequestAction::class)
                ->add(UploadFileHandler::class)
                ->add(AuthMiddleware::class);

            $group->get('', Product\GetAll\RequestAction::class)->add(AuthMiddleware::class);
            $group->put('/{productId:'.$uuidPattern.'}', Product\Update\RequestAction::class)->add(AuthMiddleware::class);

            $group->get('/{productId:'.$uuidPattern.'}', Product\Get\RequestAction::class);
            $group->get('/s/{slug:[a-z-]+}', Product\GetBySlug\RequestAction::class);

            $group->get('/free', Product\GetAllFree\RequestAction::class)->add(AuthMiddleware::class);
        });

        $group->group('/auth', function (RouteCollectorProxy $group): void {
            $group->post('/login', RequestAction::class);
        });


        $group->group('/directions', function (RouteCollectorProxy $group): void {
            $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

            $group->get('',  Direction\GetAll\RequestAction::class);

            $group->get('/s/{slug:[a-z-]+}', Direction\GetBySlug\RequestAction::class);
            $group->post('', Direction\Add\RequestAction::class);
            $group->delete('/{directionId:'.$uuidPattern.'}', Direction\Delete\RequestAction::class);
            $group->put('/{directionId:'.$uuidPattern.'}', Direction\Update\RequestAction::class);

            $group->group('/{directionId:'.$uuidPattern.'}/categories', function (RouteCollectorProxy $group) use ($uuidPattern) : void {

                $group->get('', Direction\Category\GetAll\RequestAction::class);
                $group->get('/s/{slug:[a-z-]+}', Direction\Category\GetBySlug\RequestAction::class);

                $group->post('', Direction\Category\Add\RequestAction::class)->add(AuthMiddleware::class);
                $group->put('/{categoryId:'.$uuidPattern.'}', Direction\Category\Update\RequestAction::class)->add(AuthMiddleware::class);
            });

        });

        $group->group('/categories', function (RouteCollectorProxy $group): void {
            $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

            $group->get('', Direction\Category\Admin\GetAll\RequestAction::class)->add(AuthMiddleware::class);

            $group->group('/{categoryId:'.$uuidPattern.'}', function (RouteCollectorProxy $group) : void {
                $group->put('/product', Direction\Category\AssignProduct\RequestAction::class)->add(AuthMiddleware::class);
                $group->delete('/product', Direction\Category\RefuseProduct\RequestAction::class)->add(AuthMiddleware::class);
            });


        });

    });



};