<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Provider;

use Generator;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

class FunctionConstantSymbolProvider
{
    /**
     * @return Generator<SymbolInterface>
     */
    public function provide(string $dir, array $files): Generator
    {
        foreach ($files as $file) {
            yield new Symbol($file);
        }
    }
}
