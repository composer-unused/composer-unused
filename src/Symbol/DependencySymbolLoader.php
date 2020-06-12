<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

use Composer\Package\PackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\Provider\FunctionConstantSymbolProvider;

final class DependencySymbolLoader implements SymbolLoaderInterface
{
    /** @var FunctionConstantSymbolProvider */
    private $fileSymbolProvider;
    /** @var SymbolLoaderInterface */
    private $symbolLoader;

    public function __construct(FunctionConstantSymbolProvider $fileSymbolProvider, SymbolLoaderInterface $symbolLoader)
    {
        $this->fileSymbolProvider = $fileSymbolProvider;
        $this->symbolLoader = $symbolLoader;
    }

    /**
     * @return Generator<SymbolInterface>
     */
    public function load(PackageInterface $package): Generator
    {
        yield from $this->symbolLoader->load($package);

        $files = $package->getAutoload()['files'] ?? [];
        yield from $this->fileSymbolProvider->provide($package->getTargetDir(), $files);
    }
}
