<?php

namespace App\Http\Action\V1\Direction\Delete;

use App\Direction\Command\Direction\Delete\Command;
use App\Direction\Command\Direction\Delete\Handler;
use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class RequestAction implements RequestHandlerInterface
{

    public function __construct(
     private Handler $handler,
     private Validator $validator
    ){
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);

        $route = $routeContext->getRoute();

        $directionId = $route->getArgument('directionId', '');

        $command = new Command($directionId);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(204);
    }
}