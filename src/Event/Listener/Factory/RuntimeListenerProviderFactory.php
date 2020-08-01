<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Event\Listener\Factory;

use Psr\Container\ContainerInterface;
use Icanhazstring\Composer\Unused\Event\Listener\RuntimeListenerProvider;
use Icanhazstring\Composer\Unused\Event\ListenerEventTypeResolver;

class RuntimeListenerProviderFactory
{
    public function __invoke(ContainerInterface $container): RuntimeListenerProvider
    {
        return new RuntimeListenerProvider($container->get(ListenerEventTypeResolver::class));
    }
}
