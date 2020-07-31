<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Composer\Package\PackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\NamespaceSymbol;

use function array_merge;

final class PsrSymbolLoader implements SymbolLoaderInterface
{
    public function load(PackageInterface $package): Generator
    {
        $namespaces = array_merge(
            $package->getAutoload()['psr-4'] ?? [],
            $package->getAutoload()['psr-0'] ?? []
        );

        foreach ($namespaces as $namespace => $dir) {
            yield new NamespaceSymbol($namespace);
        }
    }
}
