<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Template\Document\GetAllByCategorySlugs;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Template\Query\Document\GetAllByCategorySlugs\Handler;
use App\Template\Query\Document\GetAllByCategorySlugs\Query;
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

        $query = new Query(
            (string) $route->getArgument('directionSlug', ''),
            (string) $route->getArgument('categorySlug', ''),
        );
        $this->validator->validate($query);

        return new JsonResponse($this->handler->handle($query));
    }
}
