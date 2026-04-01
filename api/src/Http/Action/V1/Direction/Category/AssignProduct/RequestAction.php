<?php

namespace App\Http\Action\V1\Direction\Category\AssignProduct;

use App\Direction\Command\Direction\Category\AssignProduct\Command;
use App\Direction\Command\Direction\Category\AssignProduct\Handler;
use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Validator $validator,
        private readonly Handler $handler,
    )
    {}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $categoryId = $route->getArgument('categoryId', '');

        $productId = $request->getParsedBody()['productId'] ?? '';

        $command = new Command(
            $productId,
            $categoryId,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse();
    }
}