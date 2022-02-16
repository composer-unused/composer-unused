<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused;

use ComposerUnused\Contracts\LinkInterface;
use ComposerUnused\Contracts\PackageInterface;
use ComposerUnused\Contracts\RepositoryInterface;
use ComposerUnused\ComposerUnused\Composer\Package;

final class PackageResolver
{
    public function resolve(
        LinkInterface $packageLink,
        RepositoryInterface $repository
    ): ?PackageInterface {
        $isPhp = $packageLink->getTarget() === 'php';
        $isExtension = strpos($packageLink->getTarget(), 'ext-') === 0;

        if ($isPhp || $isExtension) {
            return new Package(strtolower($packageLink->getTarget()));
        }

        return $repository->findPackage($packageLink->getTarget());
    }
}
