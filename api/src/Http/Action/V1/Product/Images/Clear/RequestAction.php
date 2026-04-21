<?php

namespace App\Http\Action\V1\Product\Images\Clear;

use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use App\Product\Command\Images\Clear\Command;
use App\Product\Command\Images\Clear\Handler;
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

        $productId = $route->getArgument('productId', '');

        $command = new Command($productId);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse();

    }
}