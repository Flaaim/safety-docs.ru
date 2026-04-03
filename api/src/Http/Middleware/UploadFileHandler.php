<?php

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UploadFileHandler implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uploadedFiles = $request->getUploadedFiles();

        $fileEntry = $uploadedFiles['file'] ?? null;

        $file = is_array($fileEntry) ? ($fileEntry[0] ?? null) : $fileEntry;

        if ($file instanceof UploadedFileInterface && $file->getError() === UPLOAD_ERR_OK) {
            $request = $request->withAttribute('target_file', $file);
        }

        return $handler->handle($request);

    }
}