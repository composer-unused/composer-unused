<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Factory;

use Icanhazstring\Composer\Unused\Di\ServiceContainer;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Psr\Container\ContainerInterface;

class LoaderBuilderFactory
{
    /**
     * @param ContainerInterface&ServiceContainer $container
     * @return LoaderBuilder
     */
    public function __invoke(ContainerInterface $container): LoaderBuilder
    {
        return new LoaderBuilder($container);
    }
}
