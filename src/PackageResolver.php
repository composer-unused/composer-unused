<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused;

use ComposerUnused\Contracts\LinkInterface;
use ComposerUnused\Contracts\PackageInterface;
use ComposerUnused\Contracts\RepositoryInterface;
use Icanhazstring\Composer\Unused\Composer\Package;

final class PackageResolver
{
    public function resolve(
        LinkInterface $packageLink,
        RepositoryInterface $repository
    ): ?PackageInterface {
        $isPhp = strpos($packageLink->getTarget(), 'php') === 0;
        $isExtension = strpos($packageLink->getTarget(), 'ext-') === 0;

        if ($isPhp || $isExtension) {
            return new Package(strtolower($packageLink->getTarget()));
        }

        return $repository->findPackage($packageLink->getTarget());
    }
}
