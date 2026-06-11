<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Distribution\Newsletter\Draft;

use App\Distribution\Command\DraftNewsletter\Command;
use App\Distribution\Command\DraftNewsletter\Handler;
use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array)$request->getParsedBody() ?? [];

        $command = new Command(
            $data['subject'] ?? '',
            $data['templateId'] ?? '',
            $data['projectId'] ?? '',
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse();
    }
}