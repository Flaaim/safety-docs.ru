<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Template\Document\GetRelated;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Template\Query\Document\GetRelated\Handler;
use App\Template\Query\Document\GetRelated\Query;
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

        $relatedDocuments = $this->handler->handle($query);

        return new JsonResponse($relatedDocuments);
    }
}
