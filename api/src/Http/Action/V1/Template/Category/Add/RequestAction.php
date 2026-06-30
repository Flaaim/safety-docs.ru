<?php

namespace App\Http\Action\V1\Template\Category\Add;

use App\Template\Command\Direction\Category\Add\Command;
use App\Template\Command\Direction\Category\Add\Handler;
use App\Http\EmptyResponse;
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

        $data = (array) $request->getParsedBody();

        $command = new Command(
            (string)$route->getArgument('directionId', ''),
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['text'] ?? '',
            $data['parentId'] ?? null
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(201);
    }
}
