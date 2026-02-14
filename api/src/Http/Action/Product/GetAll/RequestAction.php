<?php

namespace App\Http\Action\Product\GetAll;

use App\Http\JsonResponse;
use App\Product\Command\GetAll\Command;
use App\Product\Command\GetAll\Handler;
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
        $page = $request->getQueryParams()['page'] ?? 1;
        $perPage = $request->getQueryParams()['perPage'] ?? 20;

        $command = new Command($page, $perPage);

        $response = $this->handler->handle($command);

        return new JsonResponse($response);
    }
}