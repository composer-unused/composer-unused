<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Composer\Package\PackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\NamespaceSymbol;

final class PsrSymbolLoader implements SymbolLoaderInterface
{
    public function load(PackageInterface $package): Generator
    {
        $psr0 = $package->getAutoload()['psr-0'] ?? [];
        $psr4 = $package->getAutoload()['psr-4'] ?? [];

        foreach ($psr0 as $namespace => $dir) {
            yield new NamespaceSymbol($namespace);
        }

        foreach ($psr4 as $namespace => $dir) {
            yield new NamespaceSymbol($namespace);
        }
    }
}
