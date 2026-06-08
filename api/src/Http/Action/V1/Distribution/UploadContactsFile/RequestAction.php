<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Distribution\UploadContactsFile;

use App\Distribution\Command\UploadContactsFile\Command;
use App\Distribution\Command\UploadContactsFile\Handler;
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
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $file = $request->getAttribute('target_file');

        if (null === $file) {
            throw new \DomainException('File is required.');
        }

        $command = new Command($file);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse();
    }
}