<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use Composer\Package\PackageInterface;
use Generator;

interface SymbolLoaderInterface
{
    /**
     * @return Generator<SymbolInterface>
     */
    public function load(PackageInterface $package): Generator;
}
