<?php

namespace App\Http\Action\V1\Template\Category\GetAll;

use App\Template\Command\Direction\Category\GetAllByDirection\Command;
use App\Template\Command\Direction\Category\GetAllByDirection\Handler;
use App\Http\JsonResponse;
use App\Http\Validator\Validator;
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

        $command = new Command($directionId);

        $this->validator->validate($command);

        $response = $this->handler->handle($command);

        return new JsonResponse($response);
    }
}
