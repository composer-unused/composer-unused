<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use OutOfBoundsException;

final class InstalledVersions
{
    /** @var array{root: array<mixed>, versions: array<string, array<mixed>>} */
    private array $installed;

    /**
     * @param array{root: array<mixed>, versions: array<string, array<mixed>>} $installed
     */
    public function __construct(array $installed)
    {
        $this->installed = $installed;
    }

    public function getInstallPath(string $name): string
    {
        $installPath = $this->installed['versions'][$name]['install_path'] ?? null;

        if ($installPath === null) {
            throw new OutOfBoundsException('Package not installed');
        }

        return $installPath;
    }
}
