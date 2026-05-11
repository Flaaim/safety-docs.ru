<?php

namespace App\Http\Validator;

use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SlimUploadedFileValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SlimUploadedFile) {
            throw new UnexpectedTypeException($constraint, SlimUploadedFile::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof UploadedFileInterface) {
            $this->context->buildViolation('Uploaded file should be an instance of UploadedFileInterface')
                ->addViolation();
            return;
        }

        if ($value->getError() !== UPLOAD_ERR_OK) {
            $this->context->buildViolation($this->getUploadErrorMessage($value->getError()))
                ->addViolation();
            return;
        }

        if ($constraint->maxSize) {
            $limit = $this->parseSize($constraint->maxSize);
            if ($value->getSize() > $limit) {
                $this->context->buildViolation($constraint->maxSizeMessage)
                    ->setParameter('{{ limit }}', $constraint->maxSize)
                    ->setParameter('{{ size }}', $value->getSize())
                    ->addViolation();
            }
        }

        if ($constraint->mimeTypes) {
            $mimeType = $value->getClientMediaType();
            if (!in_array($mimeType, $constraint->mimeTypes, true)) {
                $this->context->buildViolation($constraint->mimeTypesMessage)
                    ->setParameter('{{ type }}', $mimeType)
                    ->setParameter('{{ types }}', implode(', ', $constraint->mimeTypes))
                    ->addViolation();
            }
        }
        if ($constraint->extensions) {
            $extension = pathinfo($value->getClientFilename(), PATHINFO_EXTENSION);
            if (!in_array($extension, $constraint->extensions, true)) {
                $this->context->buildViolation($constraint->extensionsMessage)
                    ->setParameter('{{ extension }}', $extension)
                    ->setParameter('{{ extensions }}', implode(', ', $constraint->extensions))
                    ->addViolation();
            }
        }
    }

    private function parseSize(int|string $size): int
    {
        if (is_numeric($size)) {
            return (int) $size;
        }

        if (!is_string($size)) {
            throw new \InvalidArgumentException('Size must be a string or integer');
        }

        $size = strtoupper(trim($size));

        // Регулярное выражение для разбора размера
        if (!preg_match('/^(\d+)(?:\.\d+)?([KMGTP]?)(B)?$/i', $size, $matches)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid file size.', $size));
        }

        $value = (int) $matches[1];
        $unit = $matches[2] ?? '';

        return match ($unit) {
            'K' => $value * 1024,
            'M' => $value * 1024 * 1024,
            'G' => $value * 1024 * 1024 * 1024,
            'T' => $value * 1024 * 1024 * 1024 * 1024,
            'P' => $value * 1024 * 1024 * 1024 * 1024 * 1024,
            default => $value,
        };
    }
    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'The file is too large.',
            UPLOAD_ERR_FORM_SIZE => 'The file is too large.',
            UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write file to disk.',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
            default => 'Unknown upload error.',
        };
    }
}
