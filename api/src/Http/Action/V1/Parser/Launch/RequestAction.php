<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Parser\Launch;

use App\Http\JsonResponse;
use App\Parser\Command\Launch\Command;
use App\Parser\Command\Launch\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestAction implements RequestHandlerInterface
{

    public function __construct(
      private readonly Handler $handler,
    ){}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array)$request->getParsedBody() ?? [];

        $command = new Command(
            $data['url'] ?? '',
            $data['cookie'] ?? '',
        );

        $parserData = $this->handler->handle($command);

        return new JsonResponse($parserData);
    }
}