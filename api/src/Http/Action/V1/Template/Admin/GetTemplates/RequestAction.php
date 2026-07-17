<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Template\Admin\GetTemplates;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\ReadModel\Template\GetTemplatesHandler;
use App\ReadModel\Template\GetTemplatesQuery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Admin endpoint: paginated Template list with Direction/Category names.
 */
final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly GetTemplatesHandler $handler,
        private readonly Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();

        $page = (int) ($params['page'] ?? 1);
        $perPage = (int) ($params['perPage'] ?? 20);
        $directionId = isset($params['directionId']) && $params['directionId'] !== ''
            ? (string) $params['directionId']
            : null;
        $categoryId = isset($params['categoryId']) && $params['categoryId'] !== ''
            ? (string) $params['categoryId']
            : null;
        $search = isset($params['search']) && $params['search'] !== ''
            ? (string) $params['search']
            : null;

        $query = new GetTemplatesQuery(
            page: $page,
            perPage: $perPage,
            directionId: $directionId,
            categoryId: $categoryId,
            search: $search,
        );

        $this->validator->validate($query);

        $result = $this->handler->handle($query);

        return new JsonResponse($result);
    }
}
