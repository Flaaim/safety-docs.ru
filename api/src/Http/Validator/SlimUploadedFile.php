<?php

namespace App\Http\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SlimUploadedFile extends Constraint
{
    public int|string|null $maxSize = null;
    public array $mimeTypes = [];
    public array $extensions = [];

    public $mimeTypesMessage = 'The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.';
    public $maxSizeMessage = 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.';
    public $extensionsMessage = 'The extension of the file is invalid ({{ extension }}). Allowed extensions are {{ extensions }}.';
    public function getTargets(): array|string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function __construct(
        ?array $options = null,
        ?array $groups = null,
        mixed $payload = null,
        int|string|null $maxSize = null,
        array $mimeTypes = [],
        array $extensions = [],
    ) {
        parent::__construct($options, $groups, $payload);

        $this->maxSize = $maxSize ?? $this->maxSize;
        $this->mimeTypes = $mimeTypes ?? $this->mimeTypes;
        $this->extensions = $extensions ?? $this->extensions;
    }
}
