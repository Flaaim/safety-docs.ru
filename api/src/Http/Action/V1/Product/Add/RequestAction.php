<?php

namespace App\Http\Action\V1\Product\Add;

use App\Http\EmptyResponse;
use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Product\Command\Add\Handler;
use App\Product\Command\Add\Command;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAction implements RequestHandlerInterface
{

    public function __construct(
        private Validator $validator,
        private Handler   $handler
    ){

    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $file = $request->getAttribute('target_file');

        if(null === $file) {
            throw new \DomainException('File is required.');
        }

        $command = new Command(
            $data['name'] ?? '',
            $data['cipher'] ?? '',
            $data['amount'] ?? 0,
            $data['filename'] ?? '',
            $data['slug'] ?? '',
            $data['updatedAt'] ?? '',
                $file,
            $data['totalDocuments'] ?? 0,
            $data['formatDocuments'] ?? []
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(201 );
    }
}