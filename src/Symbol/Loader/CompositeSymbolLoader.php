<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecoratorInterface;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

final class CompositeSymbolLoader implements SymbolLoaderInterface
{
    /** @var array<SymbolLoaderInterface> */
    private $symbolLoader;

    /**
     * @param array<SymbolLoaderInterface> $symbolLoader
     */
    public function __construct(array $symbolLoader)
    {
        $this->symbolLoader = $symbolLoader;
    }

    /**
     * @param PackageDecoratorInterface $package
     * @return Generator<SymbolInterface>
     */
    public function load(PackageDecoratorInterface $package): Generator
    {
        foreach ($this->symbolLoader as $loader) {
            yield from $loader->load($package);
        }
    }
}
