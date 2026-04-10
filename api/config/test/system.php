<?php

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;


return [
    FileSystemPath::class => function () {
        return InMemoryFileSystemPath::create();
    },
];