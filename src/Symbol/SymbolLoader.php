<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use Composer\Package\PackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\Provider\FunctionConstantSymbolProvider;

class SymbolLoader
{
    /** @var FunctionConstantSymbolProvider */
    private $fileSymbolProvider;

    public function __construct(FunctionConstantSymbolProvider $fileSymbolProvider)
    {
        $this->fileSymbolProvider = $fileSymbolProvider;
    }

    /**
     * @return Generator<SymbolInterface>
     */
    public function load(PackageInterface $package): Generator
    {
        $psr0 = $package->getAutoload()['psr-0'] ?? [];
        $psr4 = $package->getAutoload()['psr-4'] ?? [];
        $files = $package->getAutoload()['files'] ?? [];

        foreach ($psr0 as $namespace => $dir) {
            yield new NamespaceSymbol($namespace);
        }

        foreach ($psr4 as $namespace => $dir) {
            yield new NamespaceSymbol($namespace);
        }

        yield from $this->fileSymbolProvider->provide($package->getTargetDir(), $files);
    }
}
