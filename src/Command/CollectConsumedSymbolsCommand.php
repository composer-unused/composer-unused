<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command;

use Composer\Package\PackageInterface;

final class CollectConsumedSymbolsCommand
{
    /** @var PackageInterface */
    private $package;
    /** @var string */
    private $packageRoot;

    public function __construct(?string $packageRoot, PackageInterface $package)
    {
        $this->packageRoot = $packageRoot ?? '';
        $this->package = $package;
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
}
