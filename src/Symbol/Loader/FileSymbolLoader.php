<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Composer\Util\Filesystem;
use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecoratorInterface;
use Icanhazstring\Composer\Unused\Exception\IOException;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_map;
use function array_merge;
use function preg_match;

class FileSymbolLoader implements SymbolLoaderInterface
{
    /** @var FileSymbolProvider */
    private $fileSymbolProvider;
    /** @var array<string> */
    private $autoloadTypes;

    /**
     * @param array<string> $autoloadTypes
     */
    public function __construct(FileSymbolProvider $fileSymbolProvider, array $autoloadTypes)
    {
        $this->fileSymbolProvider = $fileSymbolProvider;
        $this->autoloadTypes = $autoloadTypes;
    }

    /**
     * @throws IOException
     */
    public function load(PackageDecoratorInterface $package): Generator
    {
        $paths = [];

        foreach ($this->autoloadTypes as $autoloadType) {
            $paths[] = $this->resolvePackageSourcePath(
                $package,
                $package->getAutoload()[$autoloadType] ?? []
            );
        }

        [$sourceFiles, $sourceFolders] = $this->partitionFilesAndFolders(
            array_merge(...$paths)
        );

        $finder = new Finder();

        /** @var SplFileInfo[]|Finder $files */
        $files = $finder
            ->files()
            ->name('*.php')
            ->in($sourceFolders)
            ->append($sourceFiles)
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->ignoreUnreadableDirs()
            ->exclude(['vendor']);

        yield from $this->fileSymbolProvider->provide($files);
    }

    /**
     * @param array<string> $paths
     * @return array<string>
     */
    private function resolvePackageSourcePath(PackageDecoratorInterface $package, array $paths): array
    {
        $filesystem = new Filesystem();

        return array_map(static function (string $path) use ($package, $filesystem) {
            return $filesystem->normalizePath($package->getBaseDir() . '/' . $path);
        }, $paths);
    }

    /**
     * @param array<string> $classmapPaths
     * @return array<array<string>>
     */
    private function partitionFilesAndFolders(array $classmapPaths): array
    {
        $files = [];
        $folders = [];

        foreach ($classmapPaths as $path) {
            if ($this->isFilePath($path)) {
                $files[] = $path;
            } else {
                $folders[] = $path;
            }
        }

        return [$files, $folders];
    }

    private function isFilePath(string $path): bool
    {
        return (bool)preg_match('/\..*$/', $path);
    }
}
