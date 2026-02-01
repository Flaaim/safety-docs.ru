<?php

namespace App\Http\Test\Unit\Validator;

use App\Http\Validator\SlimUploadedFile;
use App\Http\Validator\SlimUploadedFileValidator;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class SlimUploadedFileValidatorTest extends ConstraintValidatorTestCase
{
    private array $files = [];
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new SlimUploadedFileValidator();
    }
    public function testSuccess(): void
    {
        $file = $this->buildUploadedFile('test.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 10,UPLOAD_ERR_OK);

        $this->validator->validate($file, new SlimUploadedFile());
        $this->assertNoViolation();

    }
    public function testInvalidUploadFile(): void
    {
        $file = new class(){};
        $this->validator->validate($file, new SlimUploadedFile());

        $this->buildViolation('Uploaded file should be an instance of UploadedFileInterface')
            ->assertRaised();
    }
    public function testFileError(): void
    {
        $file = $this->buildUploadedFile('test.docx', 'application/msword',10,UPLOAD_ERR_CANT_WRITE);
        $this->validator->validate($file, new SlimUploadedFile());

        $this->buildViolation('Cannot write file to disk.')
            ->assertRaised();

    }

    public function testInvalidConstraint(): void
    {
        $file = $this->buildUploadedFile('test.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 10, UPLOAD_ERR_OK);

        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(
            $file,
            new Blank()
        );

    }
    public function testInvalidSize(): void
    {
        $maxFileSize = 10;
        $uploadedFileSize = 20;
        $uploadedFile = $this->buildUploadedFile('test.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', $uploadedFileSize, UPLOAD_ERR_OK);

        $this->validator->validate($uploadedFile, new SlimUploadedFile(
            null,
            null,
            null,
            $maxFileSize,
            [],
            []
        ));
        $this->buildViolation('The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.') ->setParameter('{{ limit }}', $maxFileSize)->setParameter('{{ size }}', $uploadedFileSize)
            ->assertRaised();

    }

    public function testInvalidMimeType(): void
    {
        $allowedMimeTypes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $uploadedFile = $this->buildUploadedFile('test', 'pdf', 10, UPLOAD_ERR_OK);

        $this->validator->validate($uploadedFile, new SlimUploadedFile(
            null,
            null,
            null,
            null,
            $allowedMimeTypes,
            []
        ));

        $this->buildViolation('The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.')
            ->setParameter('{{ type }}', $uploadedFile->getClientMediaType())
            ->setParameter('{{ types }}', implode(', ', $allowedMimeTypes))
            ->assertRaised();

    }

    public function testInvalidExtension(): void
    {
        $allowedExtensions = ['docx', 'doc'];
        $extension = 'pdf';
        $uploadedFile = $this->buildUploadedFile('test.pdf', 'application/msword', 10, UPLOAD_ERR_OK);
        $this->validator->validate($uploadedFile, new SlimUploadedFile(
            null,
            null,
            null,
            null,
            [],
            $allowedExtensions
        ));

        $this->buildViolation('The extension of the file is invalid ({{ extension }}). Allowed extensions are {{ extensions }}.')
            ->setParameter('{{ extension }}', $extension)
            ->setParameter('{{ extensions }}', implode(', ', $allowedExtensions))
            ->assertRaised();
    }
    public function testInvalid(): void
    {
        $maxFileSize = 10;
        $uploadedFileSize = 20;
        $allowedMimeTypes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $allowedExtensions = ['docx', 'doc'];
        $extension = 'pdf';


        $uploadedFile = $this->buildUploadedFile('test.pdf', 'pdf', 20, UPLOAD_ERR_OK);
        $this->validator->validate($uploadedFile, new SlimUploadedFile(
            null,
            null,
            null,
            10,
            ['application/msword'],
            ['docx', 'doc']
        ));

        $this->buildViolation('The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.') ->setParameter('{{ limit }}', $maxFileSize)->setParameter('{{ size }}', $uploadedFileSize);

        $this->buildViolation('The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.')
            ->setParameter('{{ type }}', $uploadedFile->getClientMediaType())
            ->setParameter('{{ types }}', implode(', ', $allowedMimeTypes));

        $this->buildViolation('The extension of the file is invalid ({{ extension }}). Allowed extensions are {{ extensions }}.')
            ->setParameter('{{ extension }}', $extension)
            ->setParameter('{{ extensions }}', implode(', ', $allowedExtensions));

        $this->assertSame(3, $violationsCount = \count($this->context->getViolations()));

    }

    private function buildUploadedFile(string $name, string $type, int $size, int $error): UploadedFileInterface
    {
        $tempFile = tempnam(sys_get_temp_dir(), $name);
        file_put_contents($tempFile, 'data');

        $this->files[] = $tempFile;

        return new UploadedFile(
            $tempFile,
            $name,
            $type,
            $size,
            $error
        );
    }


    public function tearDown(): void
    {
        foreach ($this->files as $file) {
            unlink($file);
        }
    }


}