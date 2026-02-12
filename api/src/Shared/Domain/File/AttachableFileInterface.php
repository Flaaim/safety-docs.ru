<?php

namespace App\Shared\Domain\File;

interface AttachableFileInterface
{
    public function exists(): bool;
    public function getFile(): string;
    public function getValue(): string;
}