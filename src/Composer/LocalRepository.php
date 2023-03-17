<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\Contracts\PackageInterface;
use ComposerUnused\Contracts\RepositoryInterface;
use OutOfBoundsException;

final class LocalRepository implements RepositoryInterface
{
    private InstalledVersions $installedVersions;
    private PackageFactory $packageFactory;
    private ConfigFactory $configFactory;

    public function __construct(
        InstalledVersions $installedVersions,
        PackageFactory $packageFactory,
        ConfigFactory $configFactory
    ) {
        $this->installedVersions = $installedVersions;
        $this->packageFactory = $packageFactory;
        $this->configFactory = $configFactory;
    }

    public function findPackage(string $name): ?PackageInterface
    {
        try {
            $packageDir = $this->installedVersions->getInstallPath($name);
            $url = $this->installedVersions->getUrl($name);
        } catch (OutOfBoundsException $e) {
            return null;
        }

        $packageComposerJson = $packageDir . DIRECTORY_SEPARATOR . 'composer.json';

        if (!file_exists($packageComposerJson)) {
            return null;
        }

        try {
            $config = $this->configFactory->fromPath($packageComposerJson);
            $config->setUrl($url);
        } catch (\RuntimeException $e) {
            return null;
        }

        return $this->packageFactory->fromConfig($config);
    }
}
