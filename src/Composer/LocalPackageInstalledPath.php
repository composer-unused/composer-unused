<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

class LocalPackageInstalledPath
{
    private string $installedDir;

    public function __construct(Config $composerConfig)
    {
        $this->installedDir = $composerConfig->getBaseDir()
                        . DIRECTORY_SEPARATOR
                        . $composerConfig->get('vendor-dir')
                        . DIRECTORY_SEPARATOR
                        . 'composer';
    }

    public function getInstalledJsonPath(): string
    {
        return $this->installedDir . DIRECTORY_SEPARATOR . 'installed.json';
    }

    public function getInstalledPhpArrayPath(): string
    {
        return $this->installedDir . DIRECTORY_SEPARATOR . 'installed.php';
    }
}
