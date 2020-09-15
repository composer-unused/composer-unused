<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused;

use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use function iterable_to_traversable;

final class PackageResolver
{
    public function resolve(
        Link $package,
        InstalledRepositoryInterface $repository
    ): ?PackageInterface
    {
        $isPhp = strpos($package->getTarget(), 'php') === 0;
        $isExtension = strpos($package->getTarget(), 'ext-') === 0;

        if ($isPhp || $isExtension) {
            return new Package(
                strtolower($package->getTarget()),
                '*',
                '*'
            );
        }

        return $repository->findPackage($package->getTarget(), $package->getConstraint());
    }
}
