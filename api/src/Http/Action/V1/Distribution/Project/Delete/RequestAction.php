<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Distribution\Project\Delete;

use App\Distribution\Command\DeleteProject\Command;
use App\Distribution\Command\DeleteProject\Handler;
use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('active_route');
        $projectId = (string)$route->getArgument('projectId', '');

        $command = new Command($projectId);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse();
    }
}