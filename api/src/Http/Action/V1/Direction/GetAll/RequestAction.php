<?php

namespace App\Http\Action\V1\Direction\GetAll;

use App\Direction\Command\Direction\GetAll\Handler;
use App\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


class RequestAction implements RequestHandlerInterface
{

    public function __construct(
        private readonly Handler $handler
    ){
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $response = $this->handler->handle();

        return new JsonResponse($response);
    }
}