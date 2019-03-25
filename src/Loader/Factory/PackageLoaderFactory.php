<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Factory;

use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Psr\Container\ContainerInterface;

class PackageLoaderFactory
{
    public function __invoke(ContainerInterface $container): PackageLoader
    {
        return new PackageLoader($container->get(PackageSubjectFactory::class));
    }
}
