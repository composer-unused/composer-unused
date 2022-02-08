<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

final class LocalRepositoryFactory
{
    private PackageFactory $packageFactory;
    private ConfigFactory $configFactory;

    public function __construct(PackageFactory $packageFactory, ConfigFactory $configFactory)
    {
        $this->packageFactory = $packageFactory;
        $this->configFactory = $configFactory;
    }

    public function create(Config $composerConfig): LocalRepository
    {
        return new LocalRepository(
            new InstalledVersions(
                require $composerConfig->getBaseDir()
                        . DIRECTORY_SEPARATOR
                        . $composerConfig->get('vendor-dir')
                        . DIRECTORY_SEPARATOR
                        . 'composer'
                        . DIRECTORY_SEPARATOR
                        . 'installed.php'
            ),
            $this->packageFactory,
            $this->configFactory
        );
    }
}
