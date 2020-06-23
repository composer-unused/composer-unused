<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Composer\Package\PackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;

final class CompositeSymbolLoader implements SymbolLoaderInterface
{
    /** @var array<SymbolLoaderInterface> */
    private $symbolLoader;

    public function __construct(array $symbolLoader)
    {
        $this->symbolLoader = $symbolLoader;
    }

    /**
     * @return Generator<SymbolInterface>
     */
    public function load(PackageInterface $package): Generator
    {
        foreach ($this->symbolLoader as $loader) {
            yield from $loader->load($package);
        }
    }
}
