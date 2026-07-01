<?php

declare(strict_types=1);

use App\Http\Action\V1\Auth\GetToken\RequestAction;
use App\Http\Action\V1\Template;
use App\Http\Action\V1\Payment;
use App\Http\Action\V1\Product;
use App\Http\Action\V1\Distribution;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\UnsubscribeMiddleware;
use App\Http\Middleware\UploadFileHandler;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {

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

            $group->post('/{productId:' . $uuidPattern . '}', Product\Update\RequestAction::class)
                ->add(UploadFileHandler::class)
                ->add(AuthMiddleware::class);

            $group->get('/{productId:' . $uuidPattern . '}', Product\Get\RequestAction::class);

            $group->get('/free', Product\GetAllFree\RequestAction::class)->add(AuthMiddleware::class);

            $group->group('/{productId:' . $uuidPattern . '}/images', function (RouteCollectorProxy $group): void {
                $group->post('', Product\Images\Add\RequestAction::class);
                $group->get('', Product\Images\GetAll\RequestAction::class);

                $group->delete('', Product\Images\Clear\RequestAction::class);
            })->add(AuthMiddleware::class);
        });

        $group->group('/auth', function (RouteCollectorProxy $group): void {
            $group->post('/login', RequestAction::class);
        });


        $group->group('/directions', function (RouteCollectorProxy $group): void {
            $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

            $group->get('', Template\GetAll\RequestAction::class);

            $group->get('/s/{slug:[a-z0-9-]+}', Template\GetBySlug\RequestAction::class);
            $group->post('', Template\Add\RequestAction::class);
            $group->delete('/{directionId:' . $uuidPattern . '}', Template\Delete\RequestAction::class);
            $group->put('/{directionId:' . $uuidPattern . '}', Template\Update\RequestAction::class);

            $group->group('/{directionId:' . $uuidPattern . '}/categories', function (RouteCollectorProxy $group) use ($uuidPattern): void {

                $group->get('', Template\Category\GetAllByDirection\RequestAction::class);
                $group->get('/s/{slug:[a-z0-9-]+}', Template\Category\GetBySlug\RequestAction::class);
                $group->delete('/{categoryId:' . $uuidPattern . '}', Template\Category\Delete\RequestAction::class)->add(AuthMiddleware::class);
                $group->post('', Template\Category\Add\RequestAction::class)->add(AuthMiddleware::class);
                $group->put('/{categoryId:' . $uuidPattern . '}', Template\Category\Update\RequestAction::class)->add(AuthMiddleware::class);
            });
        });


        $group->group('/distributions', function (RouteCollectorProxy $group): void {
            $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

            $group->get('/contact-files', Distribution\GetContactFiles\RequestAction::class)->add(AuthMiddleware::class);
            $group->post(
                '/contact-files',
                Distribution\UploadContactsFile\RequestAction::class
            )
                ->add(UploadFileHandler::class)
                ->add(AuthMiddleware::class);

            $group->delete('/contact-files/{fileId:' . $uuidPattern . '}', Distribution\RemoveContactsFile\RequestAction::class)->add(AuthMiddleware::class);

            $group->post('/import-contacts', Distribution\ImportContacts\RequestAction::class)->add(AuthMiddleware::class);

            $group->group('/projects', function (RouteCollectorProxy $group): void {
                $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

                $group->post('', Distribution\Project\Create\RequestAction::class)->add(AuthMiddleware::class);
                $group->get('', Distribution\Project\GetAll\RequestAction::class)->add(AuthMiddleware::class);
                $group->delete('/{projectId:' . $uuidPattern . '}', Distribution\Project\Delete\RequestAction::class)->add(AuthMiddleware::class);
                $group->map(['GET', 'POST'], '/unsubscribe', Distribution\Project\UnsubscribeContact\RequestAction::class)->add(UnsubscribeMiddleware::class);
            });

            $group->group('/newsletters', function (RouteCollectorProxy $group): void {
                $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

                $group->post('', Distribution\Newsletter\Draft\RequestAction::class)->add(AuthMiddleware::class);
                $group->get('', Distribution\Newsletter\GetAllPaginated\RequestAction::class)->add(AuthMiddleware::class);
                $group->delete('/{newsletterId:' . $uuidPattern . '}', Distribution\Newsletter\Archive\RequestAction::class)->add(AuthMiddleware::class);
                $group->post('/launch', Distribution\Newsletter\Launch\RequestAction::class)->add(AuthMiddleware::class);
            });
        });

        $group->group('/documents', function (RouteCollectorProxy $group): void {
            $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

            $group->post('/bulk', Document\MultipleUpload\RequestAction::class)->add(AuthMiddleware::class);
        });
    });
};
