<?php

namespace App\Product\Entity;

enum FormatDocument: string
{
    case PDF = 'pdf';
    case DOCX = 'docx';
    case DOC = 'doc';
    case EXCEL = 'excel';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
