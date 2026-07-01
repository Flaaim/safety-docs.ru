<?php

namespace App\Http\Action\V1\Template\Category\GetBySlug;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Template\Query\Category\GetBySlug\Handler;
use App\Template\Query\Category\GetBySlug\Query;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('active_route');

        $slug = $route->getArgument('slug', '');
        $directionId = $route->getArgument('directionId', '');

        $command = new Query($slug, $directionId);

        $this->validator->validate($command);

        $response = $this->handler->handle($command);

        return new JsonResponse($response);
    }
}
