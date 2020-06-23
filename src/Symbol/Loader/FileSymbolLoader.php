<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Composer\Package\PackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;

class FileSymbolLoader implements SymbolLoaderInterface
{
    /** @var FileSymbolProvider */
    private $fileSymbolProvider;

    public function __construct(FileSymbolProvider $fileSymbolProvider)
    {
        $this->fileSymbolProvider = $fileSymbolProvider;
    }

    public function load(PackageInterface $package): Generator
    {
        $files = $package->getAutoload()['files'] ?? [];
        yield from $this->fileSymbolProvider->provide($package->getTargetDir(), $files);
    }
}
