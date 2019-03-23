<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject;

use Composer\Package\PackageInterface;

class PackageSubject implements SubjectInterface
{
    /** @var PackageInterface */
    private $composerPackage;

    public function __construct(PackageInterface $composerPackage)
    {
        $this->composerPackage = $composerPackage;
    }

    public function providesNamespace(string $namespace): bool
    {
        $autoload = array_merge_recursive(
            $this->composerPackage->getAutoload()['psr-0'] ?? [],
            $this->composerPackage->getAutoload()['psr-4'] ?? []
        );

        foreach ($autoload as $providedNamespace => $dir) {
            if (strpos($namespace, $providedNamespace) === 0) {
                return true;
            }
        }

        return false;
    }

    public function getName(): string
    {
        return $this->composerPackage->getName();
    }
}
