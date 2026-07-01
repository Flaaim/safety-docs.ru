<?php

namespace App\Http\Action\V1\Template\GetBySlug;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Template\Query\Direction\GetBySlug\Handler;
use App\Template\Query\Direction\GetBySlug\Query;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Validator $validator,
        private readonly Handler $handler
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('active_route');
        $slug = (string)$route->getArgument('slug');

        $query = new Query($slug);

        $this->validator->validate($query);

        $response = $this->handler->handle($query);

        return new JsonResponse($response);
    }
}
