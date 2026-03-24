<?php

namespace App\Shared\Domain\ValueObject\FileSystem;


interface FileSystemPathInterface
{
    public function getValue(): string;
}