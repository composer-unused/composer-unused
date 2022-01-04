<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command;

use ComposerUnused\Contracts\LinkInterface;
use ComposerUnused\Contracts\RepositoryInterface;

final class LoadRequiredDependenciesCommand
{
    /** @var string */
    private $baseDir;
    /** @var array<LinkInterface> */
    private $packageLinks;
    /** @var RepositoryInterface */
    private $packageRepository;

    /**
     * @param array<LinkInterface> $packageLinks
     */
    public function __construct(string $baseDir, array $packageLinks, RepositoryInterface $packageRepository)
    {
        $this->baseDir = $baseDir;
        $this->packageLinks = $packageLinks;
        $this->packageRepository = $packageRepository;
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
}
