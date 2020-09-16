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
use function file_exists;

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

        /** @var SplFileInfo[]|Finder $files */
        $files = $finder
            ->files()
            ->name('*.php')
            ->in($package->getBaseDir())
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->ignoreUnreadableDirs()
            ->exclude(
                array_merge(['vendor'])
            );

        if (file_exists($package->getBaseDir() . '/.gitignore')) {
            $files = $files->ignoreVCSIgnored(true);
        }

        yield from $this->fileSymbolProvider->provide($package->getTargetDir(), $files);
    }
}
