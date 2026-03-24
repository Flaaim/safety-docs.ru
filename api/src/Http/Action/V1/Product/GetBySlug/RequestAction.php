<?php

namespace App\Http\Action\V1\Product\GetBySlug;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Product\Command\GetBySlug\Command;
use App\Product\Command\GetBySlug\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator,
    )
    {

    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $slug = $route->getArgument('slug', '');

        $command = new Command($slug);

        $this->validator->validate($command);

        $response = $this->handler->handle($command);

        return new JsonResponse($response);
    }
}