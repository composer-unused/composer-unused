<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error\Handler;

use PhpParser\ErrorHandler;
use Throwable;

interface ErrorHandlerInterface extends ErrorHandler
{
    /**
     * @param Throwable $error
     * @return void
     * @throws Throwable
     */
    public function handle(Throwable $error): void;

    public function hasErrors(): bool;

    /**
     * @return Throwable[]
     */
    public function getErrors(): array;
}
