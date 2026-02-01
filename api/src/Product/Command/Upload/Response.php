<?php

namespace App\Product\Command\Upload;

class Response
{
    public function __construct(
        public string $name,
        public string $mime_type,
        public int $size,
        public string $path,
    )
    {}

    public static function fromArray(array $array): Response
    {
        return new self($array['name'], $array['mime_type'], $array['size'], $array['path']);
    }
}