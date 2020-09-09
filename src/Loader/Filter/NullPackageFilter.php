<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Filter;

use Composer\Package\Link;
use Composer\Repository\RepositoryInterface;
use Icanhazstring\Composer\Unused\Loader\PackageHelper;

class NullPackageFilter implements FilterInterface
{
    /** @var RepositoryInterface */
    private $repository;
    /** @var PackageHelper */
    private $packageHelper;

    public function __construct(
        RepositoryInterface $repository,
        PackageHelper $packageHelper
    ) {
        $this->repository = $repository;
        $this->packageHelper = $packageHelper;
    }

    public function match(Link $item): bool
    {
        return !$this->packageHelper->isPhpExtension($item) &&
            $this->repository->findPackage($item->getTarget(), $item->getConstraint()) === null;
    }

    public function getReason(): string
    {
        return 'Unable to locate package';
    }
}
