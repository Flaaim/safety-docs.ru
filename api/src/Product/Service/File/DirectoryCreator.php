<?php

namespace App\Product\Service\File;

class DirectoryCreator
{
    public function createDirectory($path): void
    {
        if(is_dir($path)) {
            return;
        }
        $status = mkdir($path, 0777, true);
        if($status === false){
            throw new \DomainException('Unable to create directory ' . $path);
        }
    }
}