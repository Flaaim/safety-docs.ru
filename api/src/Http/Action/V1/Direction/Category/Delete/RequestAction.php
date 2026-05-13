<?php

namespace App\Http\Action\V1\Direction\Category\Delete;

use App\Direction\Command\Direction\Category\Delete\Command;
use App\Direction\Command\Direction\Category\Delete\Handler;
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
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('active_route');

        $categoryId = $route->getArgument('categoryId', '');

        $command = new Command($categoryId);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(204);
    }
}
