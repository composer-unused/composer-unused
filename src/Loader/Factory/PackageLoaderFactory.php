<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Factory;

use Composer\Composer;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\Result;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PackageLoaderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var Composer $composer */
        $composer = $container->get(Composer::class);

        return new PackageLoader(
            $composer->getRepositoryManager()->getLocalRepository(),
            $container->get(PackageSubjectFactory::class),
            new Result(),
            $options['excludes'] ?? []
        );
    }
}
