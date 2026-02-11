<?php

namespace App\Product\Entity;

interface FileInterface
{
    public function exists(): bool;
    public function getFile(): string;
    public function getValue(): string;
}