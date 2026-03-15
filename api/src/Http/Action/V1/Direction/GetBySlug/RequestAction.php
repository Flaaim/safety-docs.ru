<?php

namespace App\Http\Action\V1\Direction\GetBySlug;

use App\Direction\Command\Direction\GetBySlug\Command;
use App\Direction\Command\Direction\GetBySlug\Handler;
use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class RequestAction implements RequestHandlerInterface
{

    public function __construct(
        private readonly Validator $validator,
        private readonly Handler $handler
    ){
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);

        $route = $routeContext->getRoute();

        $slug = $route->getArgument('slug');

        $command = new Command($slug);

        $this->validator->validate($command);

        $response = $this->handler->handle($command);

        return new JsonResponse($response);
    }
}