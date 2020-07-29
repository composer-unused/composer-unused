<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader\Factory;

use Icanhazstring\Composer\Unused\Di\FactoryInterface;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Psr\Container\ContainerInterface;

class FileSymbolLoaderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null)
    {
        return new FileSymbolLoader($container->get(FileSymbolProvider::class));
    }
}
