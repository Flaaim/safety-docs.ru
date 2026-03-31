<?php

namespace App\Http\Action\V1\Product\GetAllFree;


use App\Http\JsonResponse;
use App\Product\Command\GetAllFree\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAction implements RequestHandlerInterface
{

    public function __construct(
        private readonly Handler $handler,
    ){
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse($this->handler->handle());
    }
}