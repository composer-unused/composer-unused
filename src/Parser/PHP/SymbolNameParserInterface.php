<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP;

use Generator;

interface SymbolNameParserInterface
{
    /**
     * @return Generator<string>
     */
    public function parseSymbolNames(string $code): Generator;
}
