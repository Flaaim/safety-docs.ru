<?php

namespace App\Http\Action\V1\Direction\Category\Update;

use App\Direction\Command\Direction\Category\Update\Command;
use App\Direction\Command\Direction\Category\Update\Handler;
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
    ) {
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('active_route');
        $data = (array)$request->getParsedBody();

        $command = new Command(
            (string)$route->getArgument('categoryId', ''),
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['text'] ?? '',
            $data['directionId'] ?? $route->getArgument('directionId', ''),
            $data['parentId'] ?? null
        );
        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse();
    }
}
