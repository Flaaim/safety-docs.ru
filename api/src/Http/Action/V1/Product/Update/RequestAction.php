<?php

namespace App\Http\Action\V1\Product\Update;

use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use App\Product\Command\Update\Command;
use App\Product\Command\Update\Handler;
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
        $productId = $route->getArgument('productId', '');

        $data = $request->getParsedBody() ?? [];
        $file = $request->getAttribute('target_file', null);
        $command = new Command(
            $productId,
            $data['name'] ?? '',
            $data['cipher'] ?? '',
            $data['amount'] ?? 0,
            $data['path'] ?? '',
            $data['slug'] ?? '',
            $data['updatedAt'] ?? '',
            $file
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(204);
    }
}