<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error\Handler;

use PhpParser\Error;
use Throwable;

class CollectingErrorHandler implements ErrorHandlerInterface
{
    private $errors = [];

    /**
     * Adapter for php-parser error handler.
     *
     * @param Error $error
     * @return void
     * @throws Throwable
     */
    public function handleError(Error $error): void
    {
        $this->handle($error);
    }

    public function handle(Throwable $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return Throwable[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
