<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Template\Category\GetAllByDirectionSlug;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Template\Query\Category\GetAllByDirectionSlug\Handler;
use App\Template\Query\Category\GetAllByDirectionSlug\Query;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('active_route');
        $directionSlug = (string) $route->getArgument('directionSlug', '');

        $query = new Query($directionSlug);
        $this->validator->validate($query);

        return new JsonResponse($this->handler->handle($query));
    }
}
