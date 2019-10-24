<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Factory;

use Composer\Composer;
use Icanhazstring\Composer\Unused\Di\FactoryInterface;
use Icanhazstring\Composer\Unused\Loader\Filter\ExcludePackageFilter;
use Icanhazstring\Composer\Unused\Loader\Filter\InvalidNamespaceFilter;
use Icanhazstring\Composer\Unused\Loader\Filter\InvalidPackageTypeFilter;
use Icanhazstring\Composer\Unused\Loader\Filter\NullConstraintFilter;
use Icanhazstring\Composer\Unused\Loader\Filter\NullPackageFilter;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\Result;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Psr\Container\ContainerInterface;

class PackageLoaderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options = null)
    {
        /** @var Composer $composer */
        $composer = $container->get(Composer::class);
        $repository = $composer->getRepositoryManager()->getLocalRepository();

        return new PackageLoader(
            $repository,
            $container->get(PackageSubjectFactory::class),
            new Result(),
            [
                new ExcludePackageFilter($options['excludes'] ?? []),
                new NullConstraintFilter(),
                new NullPackageFilter($repository),
                new InvalidPackageTypeFilter($repository),
                new InvalidNamespaceFilter($repository)
            ]
        );
    }
}
