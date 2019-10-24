<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Di;

use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options = null);
}
