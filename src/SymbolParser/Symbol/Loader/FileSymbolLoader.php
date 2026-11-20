<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol\Loader;

use ComposerUnused\Contracts\PackageInterface;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use Generator;
use Symfony\Component\Finder\Finder;

use function array_map;
use function array_merge;
use function defined;
use function is_array;
use function preg_match;

use const DIRECTORY_SEPARATOR;
use const GLOB_BRACE;
use const GLOB_ONLYDIR;
use const GLOB_NOSORT;

final class FileSymbolLoader implements SymbolLoaderInterface
{
    private FileSymbolProvider $fileSymbolProvider;
    /** @var array<string> */
    private array $autoloadTypes;
    private ?string $baseDir;
    /** @var list<string> */
    private array $excludedDirs;

    /**
     * @param array<string> $autoloadTypes
     * @param list<string> $excludedDirs
     */
    public function __construct(FileSymbolProvider $fileSymbolProvider, array $autoloadTypes, array $excludedDirs = [])
    {
        $this->fileSymbolProvider = $fileSymbolProvider;
        $this->autoloadTypes = $autoloadTypes;
        $this->excludedDirs = $excludedDirs;
    }

    public function load(PackageInterface $package): Generator
    {
        $paths = [];

        foreach ($this->autoloadTypes as $autoloadType) {
            /** @var array<array-key, string|list<string>> $autoloadDefinition */
            $autoloadDefinition = $package->getAutoload()[$autoloadType] ?? [];
            $autoloadPaths = $this->normalizePsrStructure($autoloadDefinition);
            $paths[] = $this->resolvePackageSourcePath($autoloadPaths);
        }

        [$sourceFiles, $sourceFolders] = $this->partitionFilesAndFolders(
            array_merge(...$paths)
        );

        $sourceFolders = array_filter($sourceFolders, fn (string $dir): bool => $this->filterExistingDir($dir));

        if (!(empty($sourceFolders) && empty($sourceFiles))) {
            $finder = new Finder();
            $files = $finder
                ->files()
                ->in($sourceFolders)
                ->append($sourceFiles)
                ->name('*.php')
                ->ignoreDotFiles(true)
                ->ignoreVCS(true)
                ->ignoreUnreadableDirs()
                ->followLinks()
                ->exclude($this->excludedDirs);

            $this->fileSymbolProvider->appendFiles($files->getIterator());
        }

        yield from $this->fileSymbolProvider->provide();
    }

    /**
     * @param array<string, list<string>> $paths
     * @return array<string, list<string>>
     */
    private function resolvePackageSourcePath(array $paths): array
    {
        $fullPaths = [];

        foreach ($paths as $namespace => $namespacePaths) {
            $fullPaths[$namespace] = array_map(
                fn (string $path): string => $this->baseDir . DIRECTORY_SEPARATOR . $path,
                $namespacePaths
            );
        }

        return $fullPaths;
    }

    /**
     * @param array<string, list<string>> $classmapPaths
     * @return array<array<string>>
     */
    private function partitionFilesAndFolders(array $classmapPaths): array
    {
        $files = [];
        $folders = [];

        foreach ($classmapPaths as $namespacePaths) {
            foreach ($namespacePaths as $path) {
                if ($this->isFilePath($path)) {
                    $files[] = $path;
                } else {
                    $folders[] = $path;
                }
            }
        }

        return [$files, $folders];
    }

    private function isFilePath(string $path): bool
    {
        return (bool)preg_match('/\.[a-z0-9]+$/i', $path);
    }

    public function withBaseDir(?string $baseDir): SymbolLoaderInterface
    {
        $clone = clone $this;
        $clone->baseDir = $baseDir;

        return $clone;
    }

    /**
     * @param array<array-key, string|list<string>> $autoloadDefinition
     *
     * @return array<array-key, list<string>>
     */
    private function normalizePsrStructure(array $autoloadDefinition): array
    {
        return array_map(
            static fn ($value): array => is_array($value) ? $value : [$value],
            $autoloadDefinition
        );
    }

    private function filterExistingDir(string $dir): bool
    {
        if (is_dir($dir)) {
            return true;
        }

        $glob = glob($dir, (defined('GLOB_BRACE') ? GLOB_BRACE : 0) | GLOB_ONLYDIR | GLOB_NOSORT);

        return $glob !== [];
    }
}
