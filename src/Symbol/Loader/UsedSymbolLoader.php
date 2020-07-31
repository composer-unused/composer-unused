<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Composer\Package\PackageInterface;
use Generator;
use Icanhazstring\Composer\Unused\Composer\RootPackage;
use Icanhazstring\Composer\Unused\Exception\IOException;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_merge;
use function dirname;

class UsedSymbolLoader implements SymbolLoaderInterface
{
    /** @var FileSymbolProvider */
    private $fileSymbolProvider;

    public function __construct(FileSymbolProvider $fileSymbolProvider)
    {
        $this->fileSymbolProvider = $fileSymbolProvider;
    }

    /**
     * @param RootPackage&PackageInterface $package
     * @return Generator<SymbolInterface>
     * @throws IOException
     */
    public function load(PackageInterface $package): Generator
    {
        $finder = new Finder();

        /** @var SplFileInfo[] $files */
        $files = $finder
            ->files()
            ->name('*.php')
            ->in($package->getBaseDir())
            ->exclude(
                array_merge(['vendor', 'data'])
            );

        yield from $this->fileSymbolProvider->provide($package->getTargetDir(), $files);
    }
}
