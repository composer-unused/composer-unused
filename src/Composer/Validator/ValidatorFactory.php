<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer\Validator;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorFactory
{
    public static function createValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();
    }
}
