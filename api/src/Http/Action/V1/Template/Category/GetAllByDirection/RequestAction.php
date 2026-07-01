<?php

namespace App\Http\Action\V1\Template\Category\GetAllByDirection;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Template\Query\Category\GetAllByDirection\Handler;
use App\Template\Query\Category\GetAllByDirection\Query;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Validator $validator,
        private readonly Handler $handler
    ) {
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('active_route');

        $directionId = $route->getArgument('directionId');

        $command = new Query($directionId);

        $this->validator->validate($command);

        $response = $this->handler->handle($command);

        return new JsonResponse($response);
    }
}
