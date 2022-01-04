<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Di;

use Psr\Container\ContainerInterface;

class InvokableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null)
    {
        return new $requestedName();
    }
}
