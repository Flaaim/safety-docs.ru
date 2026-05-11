<?php

namespace App\Http\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    public function validate(object $object): void
    {
        $violations = $this->validator->validate($object);
        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }
}
