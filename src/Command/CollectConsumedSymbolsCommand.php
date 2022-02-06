<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command;

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\Contracts\PackageInterface;

final class CollectConsumedSymbolsCommand
{
    private PackageInterface $package;
    private string $packageRoot;
    private Configuration $configuration;

    public function __construct(?string $packageRoot, PackageInterface $package, Configuration $configuration)
    {
        $this->packageRoot = $packageRoot ?? '';
        $this->package = $package;
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

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}
