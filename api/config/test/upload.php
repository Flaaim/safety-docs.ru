<?php

declare(strict_types=1);

use App\Product\Command\Upload\Handler as UploadHandler;
use App\Product\Service\File\FileUploader;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;

return [
    UploadHandler::class => function () {
        return new UploadHandler(
            InMemoryFileSystemPath::create(),
            new FileUploader()
        );
    }
];