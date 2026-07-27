<?php

declare(strict_types=1);

use App\Http\Action\V1\Auth\GetToken\RequestAction;
use App\Http\Action\V1\Template;
use App\Http\Action\V1\Payment;
use App\Http\Action\V1\Distribution;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\UnsubscribeMiddleware;
use App\Http\Middleware\UploadFileHandler;
use App\Http\Action\V1\Parser;
use App\Http\Action\V1\Sitemap;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {

    $app->group('/v1', function (RouteCollectorProxy $group): void {

        $group->group('/payments', function (RouteCollectorProxy $group): void {
            $group->post('/process-payment', Payment\CreatePayment\RequestAction::class);
            $group->post('/payment-webhook', Payment\HookPayment\RequestAction::class);
            $group->get('/get/{token}', Payment\GetPaymentResult\RequestAction::class);
        });


        $group->group('/auth', function (RouteCollectorProxy $group): void {
            $group->post('/login', RequestAction::class);
        });

        // Admin Template (Document) read models
        $group->get('/templates', Template\Admin\GetTemplates\RequestAction::class)
            ->add(AuthMiddleware::class);

        //Admin Children Categories read models
        $group->get('/children-categories', Template\Admin\Categories\GetAllChildren\RequestAction::class)->add(AuthMiddleware::class);

        $group->group('/directions', function (RouteCollectorProxy $group): void {
            $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
            // Character classes inside {param:...} are fine; literal [...] outside is FastRoute "optional segment".
            $slugPattern = '[a-z0-9-]+';

            $group->get('', Template\GetAll\RequestAction::class);

            // Public slug hierarchy (mirrors frontend /directions/... paths)
            $group->group('/s/{directionSlug:' . $slugPattern . '}', function (RouteCollectorProxy $group) use ($slugPattern): void {
                $group->get('', Template\GetBySlug\RequestAction::class);

                $group->get('/categories', Template\Category\GetAllByDirectionSlug\RequestAction::class);
                $group->get(
                    '/categories/s/{categorySlug:' . $slugPattern . '}',
                    Template\Category\GetBySlugs\RequestAction::class
                );
                $group->get(
                    '/categories/s/{categorySlug:' . $slugPattern . '}/documents',
                    Template\Document\GetAllByCategorySlugs\RequestAction::class
                );
                $group->get(
                    '/categories/s/{categorySlug:' . $slugPattern . '}/documents/s/{templateSlug:' . $slugPattern . '}',
                    Template\Document\GetBySlugs\RequestAction::class
                );
            });

            $group->post('', Template\Add\RequestAction::class);
            $group->put('/{directionId:' . $uuidPattern . '}', Template\Update\RequestAction::class);

            // Admin / UUID-based category routes (CUD + legacy get-by-slug for edit dialog)
            $group->group('/{directionId:' . $uuidPattern . '}/categories', function (RouteCollectorProxy $group) use ($uuidPattern, $slugPattern): void {
                $group->get('', Template\Category\GetAllByDirection\RequestAction::class);
                $group->get('/s/{slug:' . $slugPattern . '}', Template\Category\GetBySlug\RequestAction::class);
                $group->delete('/{categoryId:' . $uuidPattern . '}', Template\Category\Delete\RequestAction::class)->add(AuthMiddleware::class);
                $group->post('', Template\Category\Add\RequestAction::class)->add(AuthMiddleware::class);
                $group->put('/{categoryId:' . $uuidPattern . '}', Template\Category\Update\RequestAction::class)->add(AuthMiddleware::class);

                $group->group('/{categoryId:' . $uuidPattern . '}/documents', function (RouteCollectorProxy $group) use ($uuidPattern, $slugPattern): void {
                    $group->post('/bulk', Template\Document\MultipleUpload\RequestAction::class)->add(AuthMiddleware::class);
                });
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

        $group->group('/parser', function (RouteCollectorProxy $group): void {
            $group->post('/launch', Parser\Launch\RequestAction::class)->add(AuthMiddleware::class);
        });

        $group->get('/sitemap/documents', Sitemap\GetDocuments\RequestAction::class);

        $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
        $group->get('/preview/{documentId:' . $uuidPattern . '}', Template\Document\Preview\RequestAction::class);
        $group->get('/related/{documentId: ' . $uuidPattern . '}', Template\Document\GetRelated\RequestAction::class);
    });
};
