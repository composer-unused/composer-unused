<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Di;

use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array<string, mixed> $options
     * @return object
     */
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null);
}
