<?php

namespace App\Shared\Domain\Service\Template;

use App\Product\Entity\File;


class TemplateManager
{
    private string $templateFile;
    public function __construct(
        private readonly RootPath $rootPath,
        private readonly File $file
    ){
        $this->templateFile =
            rtrim($this->rootPath->getValue(), '/') .
            DIRECTORY_SEPARATOR .
            ltrim($this->file->getPathToFile(), '/');
    }

    private function templateExists(): bool
    {
        return file_exists($this->templateFile);
    }

    public function getTemplate(): string
    {
        if (!$this->templateExists()) {
            throw new \DomainException("Template '$this->templateFile' files not exists");
        }
        return $this->templateFile;
    }

}