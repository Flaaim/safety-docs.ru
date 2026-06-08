<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Distribution\RemoveContactsFile;

use App\Distribution\Command\RemoveContactsFile\Command;
use App\Distribution\Command\RemoveContactsFile\Handler;
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
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('active_route');
        $fileId = (string)$route->getArgument('fileId', '');

        $command = new Command($fileId);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse();
    }
}