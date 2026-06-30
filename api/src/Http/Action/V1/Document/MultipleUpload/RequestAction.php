<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Document\MultipleUpload;

use App\Direction\Command\Document\MultipleUpload\Command;
use App\Direction\Command\Document\MultipleUpload\Handler;
use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
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
        $data = (array)($request->getParsedBody() ?? []);

        $command = new Command(
            (string)($data["categoryId"] ?? ''),
            (float)($data["amount"] ?? 0.00),
            (array)($data["files"] ?? ''),
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(201);
    }
}
