<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecoratorInterface;
use Icanhazstring\Composer\Unused\Exception\IOException;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_map;
use function sprintf;

class FileSymbolLoader implements SymbolLoaderInterface
{
    /** @var FileSymbolProvider */
    private $fileSymbolProvider;

    public function __construct(FileSymbolProvider $fileSymbolProvider)
    {
        $this->fileSymbolProvider = $fileSymbolProvider;
    }

    /**
     * @throws IOException
     */
    public function load(PackageDecoratorInterface $package): Generator
    {
        $classmapPaths = $this->resolvePackageSourcePath(
            $package,
            $package->getAutoload()['classmap'] ?? []
        );
        $filePaths = $this->resolvePackageSourcePath(
            $package,
            $package->getAutoload()['files'] ?? []
        );

        $finder = new Finder();

        /** @var SplFileInfo[]|Finder $files */
        $files = $finder
            ->files()
            ->name('*.php')
            ->in($classmapPaths)
            ->append($filePaths);

        yield from $this->fileSymbolProvider->provide($package->getTargetDir(), $files);
    }

    /**
     * @param array<string> $paths
     * @return array<string>
     */
    private function resolvePackageSourcePath(PackageDecoratorInterface $package, array $paths): array
    {
        return array_map(static function (string $path) use ($package) {
            return sprintf(
                '%s/vendor/%s/%s',
                $package->getBaseDir(),
                $package->getName(),
                $path
            );
        }, $paths);
    }
}
