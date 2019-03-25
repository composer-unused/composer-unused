<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\ErrorHandler;

use Throwable;

class CollectingErrorHandler implements ErrorHandlerInterface
{
    private $errors = [];

    public function handleError(Throwable $error): void
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
