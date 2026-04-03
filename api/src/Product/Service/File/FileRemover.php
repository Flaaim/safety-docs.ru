<?php

namespace App\Product\Service\File;

class FileRemover implements FileRemoverInterface
{

    public function remove(string $filePath): void
    {
        if(!file_exists($filePath)) {
            return;
        }
        $result = unlink($filePath);
        if(!$result){
            throw new \DomainException('Error deleting file '. $filePath);
        }
    }
}