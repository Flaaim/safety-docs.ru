<?php

namespace App\Http\Action\V1\Template\GetAll;

use App\Http\JsonResponse;
use App\Template\Query\Direction\GetAll\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->handler->handle();

        return new JsonResponse($response);
    }
}
