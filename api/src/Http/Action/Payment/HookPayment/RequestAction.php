<?php

namespace App\Http\Action\Payment\HookPayment;

use App\Http\EmptyResponse;
use App\Http\JsonResponse;
use App\Payment\Command\HookPayment\Command;
use App\Payment\Command\HookPayment\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(private readonly Handler $handler)
    {}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $request->getParsedBody() ?? [];

            if(json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['message' => json_last_error_msg()], 400);
            }

            $command = new Command($data);
            $this->handler->handle($command);
            return new EmptyResponse();
        }catch (\RuntimeException|\Exception $e){
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}