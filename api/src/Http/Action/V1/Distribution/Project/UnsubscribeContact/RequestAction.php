<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Distribution\Project\UnsubscribeContact;

use App\Distribution\Command\UnsubscribeContact\Command;
use App\Distribution\Command\UnsubscribeContact\Handler;
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
        /** @var array<array-key, string> $emails */
        $emails = $request->getParsedBody() ?? [];
        if (empty($emails)) {
            return new EmptyResponse(200);
        }

        $command = new Command($emails);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(200);
    }
}
