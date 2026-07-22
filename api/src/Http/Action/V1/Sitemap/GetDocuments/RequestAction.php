<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Sitemap\GetDocuments;

use App\Http\JsonResponse;
use App\Sitemap\Query\GetData\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $documents = $this->handler->handle();

        return new JsonResponse($documents);
    }
}
