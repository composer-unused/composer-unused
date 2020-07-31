<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecoratorInterface;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

interface SymbolLoaderInterface
{
    /**
     * @param PackageDecoratorInterface $package
     * @return Generator<SymbolInterface>
     */
    public function load(PackageDecoratorInterface $package): Generator;
}
