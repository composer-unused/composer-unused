<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecoratorInterface;
use Icanhazstring\Composer\Unused\Exception\IOException;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_merge;

class UsedSymbolLoader implements SymbolLoaderInterface
{
    /** @var FileSymbolProvider */
    private $fileSymbolProvider;

    public function __construct(FileSymbolProvider $fileSymbolProvider)
    {
        $this->fileSymbolProvider = $fileSymbolProvider;
    }

    /**
     * @param PackageDecoratorInterface $package
     * @return Generator<string, SymbolInterface>
     * @throws IOException
     */
    public function load(PackageDecoratorInterface $package): Generator
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
