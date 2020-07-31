<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Composer;

use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use DateTime;

class PackageDecorator implements PackageDecoratorInterface
{
    /** @var PackageInterface */
    private $package;
    /** @var string */
    private $baseDir;

    private function __construct(PackageInterface $package)
    {
        $this->package = $package;
    }

    public static function withBaseDir(string $baseDir, PackageInterface $package): PackageDecorator
    {
        $root = new self($package);
        $root->baseDir = $baseDir;

        return $root;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function getName(): string
    {
        return $this->package->getName();
    }

    public function getPrettyName(): string
    {
        return $this->package->getPrettyName();
    }

    public function getNames($provides = true): array
    {
        return $this->package->getNames();
    }

    public function setId($id): void
    {
        $this->package->setId($id);
    }

    public function getId(): int
    {
        return $this->package->getId();
    }

    public function isDev(): bool
    {
        return $this->package->isDev();
    }

    public function getType(): string
    {
        return $this->package->getType();
    }

    public function getTargetDir(): ?string
    {
        return $this->package->getTargetDir();
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtra(): array
    {
        return $this->package->getExtra();
    }

    public function setInstallationSource($type): void
    {
        $this->package->setInstallationSource($type);
    }

    public function getInstallationSource(): string
    {
        return $this->package->getInstallationSource();
    }

    public function getSourceType(): string
    {
        return $this->package->getSourceType();
    }

    public function getSourceUrl(): string
    {
        return $this->package->getSourceUrl();
    }

    public function getSourceUrls(): array
    {
        return $this->package->getSourceUrls();
    }

    public function getSourceReference(): string
    {
        return $this->package->getSourceReference();
    }

    /**
     * @return array<string>
     */
    public function getSourceMirrors(): ?array
    {
        return $this->package->getSourceMirrors();
    }

    public function getDistType(): string
    {
        return $this->package->getDistType();
    }

    public function getDistUrl(): string
    {
        return $this->package->getDistUrl();
    }

    public function getDistUrls(): array
    {
        return $this->package->getDistUrls();
    }

    public function getDistReference(): string
    {
        return $this->package->getDistReference();
    }

    public function getDistSha1Checksum(): string
    {
        return $this->package->getDistSha1Checksum();
    }

    /**
     * @return array<string>|null
     */
    public function getDistMirrors(): ?array
    {
        return $this->package->getDistMirrors();
    }

    public function getVersion(): string
    {
        return $this->package->getVersion();
    }

    public function getPrettyVersion(): string
    {
        return $this->package->getPrettyVersion();
    }

    public function getFullPrettyVersion($truncate = true, $displayMode = self::DISPLAY_SOURCE_REF_IF_DEV): string
    {
        return $this->package->getFullPrettyVersion();
    }

    public function getReleaseDate(): DateTime
    {
        return $this->package->getReleaseDate();
    }

    public function getStability(): string
    {
        return $this->package->getStability();
    }

    public function getRequires(): array
    {
        return $this->package->getRequires();
    }

    public function getConflicts(): array
    {
        return $this->package->getConflicts();
    }

    public function getProvides(): array
    {
        return $this->package->getProvides();
    }

    public function getReplaces(): array
    {
        return $this->package->getReplaces();
    }

    public function getDevRequires(): array
    {
        return $this->package->getDevRequires();
    }

    public function getSuggests(): array
    {
        return $this->package->getSuggests();
    }

    /**
     * @return array<string, array>
     */
    public function getAutoload(): array
    {
        return $this->package->getAutoload();
    }

    /**
     * @return array<string, array>
     */
    public function getDevAutoload(): array
    {
        return $this->package->getDevAutoload();
    }

    public function getIncludePaths(): array
    {
        return $this->package->getIncludePaths();
    }

    public function setRepository(RepositoryInterface $repository): void
    {
        $this->package->setRepository($repository);
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->package->getRepository();
    }

    public function getBinaries(): array
    {
        return $this->package->getBinaries();
    }

    public function getUniqueName(): string
    {
        return $this->package->getUniqueName();
    }

    public function getNotificationUrl(): string
    {
        return $this->package->getNotificationUrl();
    }

    public function __toString()
    {
        return $this->package->__toString();
    }

    public function getPrettyString(): string
    {
        return $this->package->getPrettyString();
    }

    /**
     * @return array<string>
     */
    public function getArchiveName(): array
    {
        return $this->package->getArchiveName();
    }

    /**
     * @return array<string>
     */
    public function getArchiveExcludes(): array
    {
        return $this->package->getArchiveExcludes();
    }

    /**
     * @return array<string>
     */
    public function getTransportOptions(): array
    {
        return $this->package->getTransportOptions();
    }

    public function setSourceReference($reference): void
    {
        $this->package->setSourceReference($reference);
    }

    public function setDistUrl($url): void
    {
        $this->package->setDistUrl($url);
    }

    public function setDistType($type): void
    {
        $this->package->setDistType($type);
    }

    public function setDistReference($reference): void
    {
        $this->package->setDistReference($reference);
    }

    public function setSourceDistReferences($reference): void
    {
        $this->package->setSourceDistReferences($reference);
    }
}
