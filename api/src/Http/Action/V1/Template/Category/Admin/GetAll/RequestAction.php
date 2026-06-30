<?php

namespace App\Http\Action\V1\Template\Category\Admin\GetAll;

use App\Template\Command\Direction\Category\Admin\GetAll\Command;
use App\Template\Command\Direction\Category\Admin\GetAll\Handler;
use App\Http\JsonResponse;
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
        $command = new Command();

        $response = $this->handler->handle($command);

        return new JsonResponse($response);
    }
}
