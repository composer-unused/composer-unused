<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Composer\Package\PackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

interface SymbolLoaderInterface
{
    /**
     * @return Generator<SymbolInterface>
     */
    public function load(PackageInterface $package): Generator;
}
