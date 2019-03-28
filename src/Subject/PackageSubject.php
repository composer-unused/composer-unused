<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject;

use Composer\Package\PackageInterface;

class PackageSubject implements SubjectInterface, SuggestedSubjectInterface, RequiredSubjectInterface
{
    /** @var PackageInterface */
    private $composerPackage;
    /** @var string[] */
    private $suggestedBy = [];
    /** @var string[] */
    private $requiredBy = [];

    public function __construct(PackageInterface $composerPackage)
    {
        $this->composerPackage = $composerPackage;
    }

    public function providesNamespace(string $usedNamespace): bool
    {
        $autoload = array_merge_recursive(
            $this->composerPackage->getAutoload()['psr-0'] ?? [],
            $this->composerPackage->getAutoload()['psr-4'] ?? [],
            $this->composerPackage->getDevAutoload()['psr-0'] ?? [],
            $this->composerPackage->getDevAutoload()['psr-4'] ?? []
        );

        foreach ($autoload as $providedNamespace => $dir) {
            if (strpos($usedNamespace, rtrim($providedNamespace, '\\')) === 0) {
                return true;
            }
        }

        return false;
    }

    public function getName(): string
    {
        return $this->composerPackage->getName();
    }

    public function suggestsPackage(string $packageName): bool
    {
        return array_key_exists($packageName, $this->composerPackage->getSuggests());
    }

    public function addSuggestedBy(string $packageName): void
    {
        $this->suggestedBy[] = $packageName;
    }

    public function getSuggestedBy(): array
    {
        return $this->suggestedBy;
    }

    public function addRequiredBy(string $packageName): void
    {
        $this->requiredBy[] = $packageName;
    }

    public function requiresPackage(string $packageName): bool
    {
        return array_key_exists($packageName, $this->composerPackage->getRequires());
    }

    public function getRequiredBy(): array
    {
        return $this->requiredBy;
    }
}
