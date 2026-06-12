<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Distribution\GetContactFiles;

use App\Distribution\Query\GetAllFilesPaginated\Fetcher;
use App\Distribution\Query\GetAllFilesPaginated\Query;
use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Fetcher $fetcher,
        private readonly Validator $validator,
    ) {
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $page = (int)($request->getQueryParams()['page'] ?? 1);
        $perPage = (int)($request->getQueryParams()['perPage'] ?? 20);

        $query = new Query($page, $perPage);

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonResponse($result);
    }
}
