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
use Psr\Log\LoggerInterface;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('active_route');
        $queryParams = $request->getQueryParams();

        $page = isset($queryParams['page']) && is_numeric($queryParams['page']) ? (int) $queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) && is_numeric($queryParams['limit']) ? (int) $queryParams['limit'] : 15;
        $search = isset($queryParams['search']) && is_string($queryParams['search']) ? $queryParams['search'] : null;

        $query = new Query(
            (string) $route->getArgument('directionSlug', ''),
            (string) $route->getArgument('categorySlug', ''),
            $page,
            $limit,
            $search
        );
        $this->validator->validate($query);

        return new JsonResponse($this->handler->handle($query));
    }
}
