<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Template\Document\GetById;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Template\Query\Document\GetById\Handler;
use App\Template\Query\Document\GetById\Query;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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

        $documentId = $route->getArgument('documentId', '');

        $query = new Query($documentId);

        $this->validator->validate($query);

        $document = $this->handler->handle($query);

        return new JsonResponse($document, 200);
    }
}
