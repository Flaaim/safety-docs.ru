<?php

namespace App\Http\Action\Product\Upload;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\Product\Command\Upload\Command;
use App\Product\Command\Upload\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator,
    )
    {}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $uploadedFile = $request->getUploadedFiles()['file'] ?? null;
        if(is_array($uploadedFile)) {
            return new JsonResponse(['errors' => [
                'multipleFiles' => 'Only one uploaded file is allowed',
            ]], 422);
        }
        $targetPath = $request->getParsedBody()['path'] ?? '';

        $command = new Command($uploadedFile, $targetPath);

        $this->validator->validate($command);

        $response = $this->handler->handle($command);

        return new JsonResponse($response);

    }
}