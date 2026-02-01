<?php

namespace App\Http\Action\Product\Upsert;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Product\Command\Upsert\Command;
use App\Product\Command\Upsert\Handler;
use Psr\Container\ContainerInterface;
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
        $data = $request->getParsedBody() ?? [];

        $command = new Command(
            $data['name'] ?? '',
            $data['cipher'] ?? '',
            $data['amount'] ?? 0,
            $data['path'] ?? '',
            $data['course'] ?? ''
        );

        $this->validator->validate($command);

        $response = $this->handler->handle($command);

        return new JsonResponse($response, 201);
    }
}