<?php

declare(strict_types=1);

use App\Product\Service\UploadFileHandler;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use App\Product\Command\Add\Upload\Handler as UploadHandler;

return [
    UploadHandler::class => function () {
        return new UploadHandler(
            InMemoryFileSystemPath::create(),
            new UploadFileHandler()
        );
    }
];