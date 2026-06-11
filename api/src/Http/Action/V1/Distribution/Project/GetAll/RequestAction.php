<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Distribution\Project\GetAll;

use App\Distribution\Query\GetAllProjects\Handler;
use App\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->handler->handle();

        return new JsonResponse($result);
    }
}
