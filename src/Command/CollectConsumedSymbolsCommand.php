<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command;

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\Contracts\PackageInterface;

final class CollectConsumedSymbolsCommand
{
    private PackageInterface $package;
    private string $packageRoot;
    /** @var list<string> */
    private array $excludedDirs;
    private Configuration $configuration;

    /**
     * @param list<string> $excludedDirs
     */
    public function __construct(?string $packageRoot, PackageInterface $package, array $excludedDirs, Configuration $configuration)
    {
        $this->packageRoot = $packageRoot ?? '';
        $this->package = $package;
        $this->excludedDirs = $excludedDirs;
        $this->configuration = $configuration;
    }

    public function getPackage(): PackageInterface
    {
        return $this->package;
    }

    /**
     * Returns the root directory where the $package is located
     */
    public function getPackageRoot(): string
    {
        return $this->packageRoot;
    }
    /**
     * @return list<string>
     */
    public function getExcludedDirs(): array
    {
        return $this->excludedDirs;
    }


    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}
