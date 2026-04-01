<?php

namespace App\Http\Action\V1\Direction\Category\RefuseProduct;

use App\Direction\Command\Direction\Category\RefuseProduct\Command;
use App\Direction\Command\Direction\Category\RefuseProduct\Handler;
use App\Http\EmptyResponse;
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

        $categoryId = $route->getArgument('categoryId', '');

        $command = new Command($categoryId);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse();
    }
}