<?php

namespace ComposerUnused\SymbolParser\Parser\PHP;

use PhpParser\Error;
use PhpParser\ErrorHandler;

/**
 * @author Laurent Laville
 */
interface ParserErrorCollectorInterface extends ErrorHandler
{
    /**
     * @return Error[]
     */
    public function getErrors(): array;

    public function hasErrors(): bool;

    /**
     * @return void explicitly specified to be compatible with \PhpParser\ErrorHandler\Collecting specification
     */
    public function clearErrors();
}
