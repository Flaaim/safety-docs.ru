<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Template\Admin\Categories\GetAllChildren;

use App\Http\JsonResponse;
use App\Template\Query\Category\GetChildrenCategories\Handler;
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
        return new JsonResponse($this->handler->handle());
    }
}
