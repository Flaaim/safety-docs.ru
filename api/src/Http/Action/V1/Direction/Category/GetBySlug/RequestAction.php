<?php

namespace App\Http\Action\V1\Direction\Category\GetBySlug;

use App\Direction\Command\Direction\Category\GetBySlug\Command;
use App\Direction\Command\Direction\Category\GetBySlug\Handler;
use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(
      private readonly Handler $handler,
      private readonly Validator $validator
    ){
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);

        $route = $routeContext->getRoute();

        $slug = $route->getArgument('slug', '');
        $directionId = $route->getArgument('directionId', '');

        $command = new Command($slug, $directionId);

        $this->validator->validate($command);

        $response = $this->handler->handle($command);

        return new JsonResponse($response);
    }
}