<?php

declare(strict_types=1);

use App\Http\Middleware;
use App\Http\Middleware\RouteMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return static function (App $app): void {

    $app->add(Middleware\ClearInputHandler::class);
    $app->addBodyParsingMiddleware();
    $app->add(RouteMiddleware::class);
    $app->addRoutingMiddleware();
    $app->add(Middleware\ValidationExceptionHandler::class);
    $app->add(Middleware\DomainExceptionHandler::class);
    $app->add(ErrorMiddleware::class);

};
