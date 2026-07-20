<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Parser\Launch;

use App\Http\EmptyResponse;
use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Parser\Command\Launch\Command;
use App\Parser\Command\Launch\Handler;
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
        $data = $request->getParsedBody() ?? [];

        $command = new Command(
            $data['categoryId'] ?? '',
            $data['amount'] ?? 0,
            $data['url'] ?? '',
            $data['cookie'] ?? '',
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse();
    }
}
