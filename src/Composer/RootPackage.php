<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Composer;

use Composer\Package\RootPackageInterface;
use Composer\Repository\RepositoryInterface;
use DateTime;

class RootPackage implements RootPackageInterface
{
    /** @var RootPackageInterface */
    private $package;
    /** @var string */
    private $baseDir;

    private function __construct(RootPackageInterface $package)
    {
        $this->package = $package;
    }

    public static function withBaseDir(string $baseDir, RootPackageInterface $package): RootPackage
    {
        $root = new self($package);
        $root->baseDir = $baseDir;

        return $root;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function getScripts()
    {
        return $this->package->getScripts();
    }

    public function getRepositories(): array
    {
        return $this->package->getRepositories();
    }

    public function getLicense(): array
    {
        return $this->package->getLicense();
    }

    public function getKeywords(): array
    {
        return $this->package->getKeywords();
    }

    public function getDescription(): string
    {
        return $this->package->getDescription();
    }

    public function getHomepage(): string
    {
        return $this->package->getHomepage();
    }

    public function getAuthors(): array
    {
        return $this->package->getAuthors();
    }

    public function getSupport(): array
    {
        return $this->package->getSupport();
    }

    public function getFunding(): array
    {
        return $this->package->getFunding();
    }

    public function isAbandoned(): bool
    {
        return $this->package->isAbandoned();
    }

    public function getReplacementPackage(): string
    {
        return $this->package->getReplacementPackage();
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

    public function getInstallationSource()
    {
        return $this->package->getInstallationSource();
    }

    public function getSourceType()
    {
        return $this->package->getSourceType();
    }

    public function getSourceUrl()
    {
        return $this->package->getSourceUrl();
    }

    public function getSourceUrls()
    {
        return $this->package->getSourceUrls();
    }

    public function getSourceReference()
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

    /**
     * @return array<string>
     */
    public function getAliases(): array
    {
        return $this->package->getAliases();
    }

    public function getMinimumStability(): string
    {
        return $this->package->getMinimumStability();
    }

    /**
     * @return array<string, string>
     */
    public function getStabilityFlags(): array
    {
        return $this->package->getStabilityFlags();
    }

    /**
     * @return array<string, string>
     */
    public function getReferences(): array
    {
        return $this->package->getReferences();
    }

    public function getPreferStable(): bool
    {
        return $this->package->getPreferStable();
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->package->getConfig();
    }

    public function setRequires(array $requires): void
    {
        $this->package->setRequires($requires);
    }

    public function setDevRequires(array $devRequires): void
    {
        $this->package->setDevRequires($devRequires);
    }

    public function setConflicts(array $conflicts): void
    {
        $this->package->setConflicts($conflicts);
    }

    public function setProvides(array $provides): void
    {
        $this->package->setProvides($provides);
    }

    public function setReplaces(array $replaces): void
    {
        $this->package->setReplaces($replaces);
    }

    /**
     * @param array<string, array> $repositories
     */
    public function setRepositories($repositories): void
    {
        $this->package->setRepositories($repositories);
    }

    /**
     * @param array<string, array> $autoload
     */
    public function setAutoload(array $autoload): void
    {
        $this->package->setAutoload($autoload);
    }

    /**
     * @param array<string, array> $devAutoload
     */
    public function setDevAutoload(array $devAutoload): void
    {
        $this->package->setDevAutoload($devAutoload);
    }

    /**
     * @param array<string, string> $stabilityFlags
     */
    public function setStabilityFlags(array $stabilityFlags): void
    {
        $this->package->setStabilityFlags($stabilityFlags);
    }

    /**
     * @param array<string, string> $suggests
     */
    public function setSuggests(array $suggests): void
    {
        $this->package->setSuggests($suggests);
    }

    /**
     * @param array<string, mixed> $extra
     */
    public function setExtra(array $extra): void
    {
        $this->package->setExtra($extra);
    }
}
