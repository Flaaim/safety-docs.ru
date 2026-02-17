<?php

namespace App\Http\Action\V1\Payment\GetPaymentResult;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Payment\Command\GetPaymentResult\Command;
use App\Payment\Command\GetPaymentResult\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;


class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    )
    {}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
            $routeContext = RouteContext::fromRequest($request);
            $route = $routeContext->getRoute();

            $token = $route->getArgument("token") ?? '';

            $command = new Command($token);

            $this->validator->validate($command);

            $response = $this->handler->handle($command);

            return new JsonResponse($response, 200);

    }
}