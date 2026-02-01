<?php

namespace App\Http\Action\Payment\Result;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Payment\Command\GetPaymentResult\Command;
use App\Payment\Command\GetPaymentResult\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    )
    {}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
            $returnToken = $request->getParsedBody()['returnToken'] ?? '';

            $command = new Command($returnToken);

            $this->validator->validate($command);

            $response = $this->handler->handle($command);

            return new JsonResponse($response, 200);

    }
}