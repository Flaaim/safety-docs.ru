<?php

use App\Shared\Domain\ValueObject\FileSystem\DistributionSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\ImageSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;

return [
    FileSystemPath::class => function () {
        return InMemoryFileSystemPath::createReal();
    },
    ImageSystemPath::class => function () {
        return InMemoryFileSystemPath::createReal();
    },
    DistributionSystemPath::class => function () {
        return InMemoryFileSystemPath::createReal();
    }
];
