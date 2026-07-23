<?php

declare(strict_types=1);

namespace App\Template\Service;

use DomainException;
use PhpOffice\PhpWord\IOFactory;

/** @psalm-suppress UnusedClass */
final class DocumentPreviewer
{
    public function getHtml(string $absoluteFilePath): string
    {
        if (!file_exists($absoluteFilePath)) {
            throw new DomainException('Файл документа не найден на сервере.');
        }
        $phpWord = IOFactory::load($absoluteFilePath);
        $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

        $tempFile = tempnam(sys_get_temp_dir(), 'preview_');

        if ($tempFile === false) {
            throw new DomainException('Не удалось создать временный файл для превью.');
        }

        try {
            $htmlWriter->save($tempFile);
            $htmlContent = file_get_contents($tempFile);
            if ($htmlContent === false) {
                throw new DomainException('Не удалось прочитать документ: ' . $absoluteFilePath);
            }
        } finally {
            // Гарантированно удаляем временный файл, даже если произошла ошибка
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }

        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $htmlContent, $matches)) {
            return $matches[1];
        }

        return $htmlContent;
    }
}
