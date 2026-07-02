<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Template\Document\MultipleUpload;

use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use App\Template\Command\Document\MultipleUpload\Command;
use App\Template\Command\Document\MultipleUpload\Handler;
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

        $route = $request->getAttribute('active_route');

        $categoryId = $route->getArgument('categoryId', '');
        $command = new Command(
            $categoryId,
            (float)($data["amount"] ?? 0.00),
            (array)($data["files"] ?? ''),
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(201);
    }
}
