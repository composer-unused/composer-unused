<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Filter;

use Composer\Package\Link;
use Composer\Repository\RepositoryInterface;

class InvalidNamespaceFilter implements FilterInterface
{
    /** @var RepositoryInterface */
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function match(Link $item): bool
    {
        $package = $this->repository->findPackage($item->getTarget(), $item->getConstraint() ?? '');

        if ($package === null) {
            return false;
        }

        $autoload = array_merge_recursive(
            $package->getAutoload()['psr-0'] ?? [],
            $package->getAutoload()['psr-4'] ?? [],
            $package->getDevAutoload()['psr-0'] ?? [],
            $package->getDevAutoload()['psr-4'] ?? []
        );

        $namespaces = array_filter(array_keys($autoload), static function ($namespace) {
            return !empty($namespace);
        });

        return empty($namespaces);
    }

    public function getReason(): string
    {
        return 'Package provides no namespace';
    }
}
