<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error;

use PhpParser\ErrorHandler;
use Throwable;

interface ErrorHandlerInterface extends ErrorHandler
{
    /**
     * @param Throwable $error
     * @return void
     */
    public function handle(Throwable $error): void;
}
