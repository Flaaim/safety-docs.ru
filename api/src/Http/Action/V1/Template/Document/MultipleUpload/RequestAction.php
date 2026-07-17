<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Template\Document\MultipleUpload;

use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use App\Template\Command\Document\MultipleUpload\Command;
use App\Template\Command\Document\MultipleUpload\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
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
            (float)($data['amount'] ?? 0.00),
            $this->collectFiles($request, $data),
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(201);
    }

    /**
     * @param array<string, mixed> $data
     * @return list<UploadedFileInterface>
     */
    private function collectFiles(ServerRequestInterface $request, array $data): array
    {
        if (isset($data['files']) && is_array($data['files'])) {
            $fromBody = array_values(array_filter(
                $data['files'],
                static fn (mixed $file): bool => $file instanceof UploadedFileInterface
            ));

            if ($fromBody !== []) {
                return $fromBody;
            }
        }

        $uploaded = $request->getUploadedFiles();

        if (!isset($uploaded['files'])) {
            return [];
        }

        return array_values(array_filter(
            $this->flattenUploadedFiles($uploaded['files']),
            static fn (mixed $file): bool => $file instanceof UploadedFileInterface
                && $file->getError() !== UPLOAD_ERR_NO_FILE
        ));
    }

    /**
     * @return list<UploadedFileInterface>
     */
    private function flattenUploadedFiles(mixed $files): array
    {
        if ($files instanceof UploadedFileInterface) {
            return [$files];
        }

        if (!is_array($files)) {
            return [];
        }

        $result = [];
        foreach ($files as $file) {
            foreach ($this->flattenUploadedFiles($file) as $uploadedFile) {
                $result[] = $uploadedFile;
            }
        }

        return $result;
    }
}
