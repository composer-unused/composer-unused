<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Filter;

use Composer\Package\Link;
use Composer\Repository\RepositoryInterface;

class InvalidPackageTypeFilter implements FilterInterface
{
    /** @var string[] */
    private $validTypes;
    /** @var RepositoryInterface */
    private $repository;

    /**
     * @param RepositoryInterface $repository
     * @param string[]            $validTypes
     */
    public function __construct(RepositoryInterface $repository, array $validTypes)
    {
        $this->validTypes = $validTypes;
        $this->repository = $repository;
    }

    public function match(Link $item): bool
    {
        /** @var string $constraint */
        $constraint = $item->getConstraint();

        $package = $this->repository->findPackage($item->getTarget(), $constraint);

        if ($package === null) {
            return false;
        }

        return !in_array($package->getType(), $this->validTypes, true);
    }

    public function getReason(): string
    {
        return 'Invalid package type';
    }
}
