<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Filter;

use Composer\Package\Link;
use Composer\Repository\RepositoryInterface;

class InvalidPackageTypeFilter implements FilterInterface
{
    private const INVALID_TYPES = ['project', 'metapackage', 'composer-plugin'];
    /** @var RepositoryInterface */
    private $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function match(Link $item): bool
    {
        $package = $this->repository->findPackage($item->getTarget(), $item->getConstraint());

        if ($package === null) {
            return false;
        }

        return in_array($package->getType(), self::INVALID_TYPES, true);
    }

    public function getReason(): string
    {
        return 'Invalid package type';
    }
}
