<?php

namespace App\Http\Action\V1\Product\Get;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Product\Command\Get\Command;
use App\Product\Command\Get\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private Validator $validator,
        private Handler   $handler
    ){

    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $productId = $route->getArgument('productId', '');

        $command = new Command($productId);

        $this->validator->validate($command);

        $response = $this->handler->handle($command);

        return new JsonResponse($response, 200);
    }
}