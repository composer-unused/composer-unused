<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command;

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Console\Progress\ProgressBarInterface;
use ComposerUnused\Contracts\LinkInterface;
use ComposerUnused\Contracts\RepositoryInterface;

final class LoadRequiredDependenciesCommand
{
    private string $baseDir;
    /** @var array<LinkInterface> */
    private array $packageLinks;
    private RepositoryInterface $packageRepository;
    private Configuration $configuration;
    private ProgressBarInterface $progressBar;

    /**
     * @param array<LinkInterface> $packageLinks
     */
    public function __construct(
        string $baseDir,
        array $packageLinks,
        RepositoryInterface $packageRepository,
        Configuration $configuration,
        ProgressBarInterface $progressBar
    ) {
        $this->baseDir = $baseDir;
        $this->packageLinks = $packageLinks;
        $this->packageRepository = $packageRepository;
        $this->configuration = $configuration;
        $this->progressBar = $progressBar;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    /**
     * @return array<LinkInterface>
     */
    public function getPackageLinks(): array
    {
        return $this->packageLinks;
    }

    public function getPackageRepository(): RepositoryInterface
    {
        return $this->packageRepository;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getProgressBar(): ProgressBarInterface
    {
        return $this->progressBar;
    }
}
