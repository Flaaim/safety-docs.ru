<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Distribution\GetContactFiles;

use App\Distribution\Command\GetContactFiles\Command;
use App\Distribution\Command\GetContactFiles\Handler;
use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator,
    ) {}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $page = (int)($request->getQueryParams()['page'] ?? 1);
        $perPage = (int)($request->getQueryParams()['perPage'] ?? 20);

        $command = new Command($page, $perPage);

        $this->validator->validate($command);

        $result = $this->handler->handle($command);

        return new JsonResponse($result);
    }
}