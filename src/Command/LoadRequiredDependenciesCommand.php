<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command;

use Composer\Package\Link;
use Composer\Repository\RepositoryInterface;

final class LoadRequiredDependenciesCommand
{
    /** @var string */
    private $baseDir;
    /** @var array<Link> */
    private $packageLinks;
    /** @var RepositoryInterface */
    private $packageRepository;

    /**
     * @param array<Link> $packageLinks
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
     * @return array<Link>
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
