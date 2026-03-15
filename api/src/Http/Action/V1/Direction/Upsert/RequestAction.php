<?php

namespace App\Http\Action\V1\Direction\Upsert;

use App\Direction\Command\Direction\Upsert\Command;
use App\Direction\Command\Direction\Upsert\Handler;
use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Validator $validator,
        private readonly Handler $handler
    ){
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $directionId = $request->getParsedBody()['direction_id'] ?? Uuid::uuid4()->toString();
        $title = $request->getParsedBody()['title'] ?? '';
        $description = $request->getParsedBody()['description'] ?? '';
        $slug = $request->getParsedBody()['slug'] ?? '';
        $text = $request->getParsedBody()['text'] ?? '';

        $command = new Command(
            $directionId,
            $title,
            $description,
            $text,
            $slug
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(201);
    }
}