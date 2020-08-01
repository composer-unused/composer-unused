<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Event\Factory;

use Icanhazstring\Composer\Unused\Event\EventDispatcher;
use Icanhazstring\Composer\Unused\Event\Listener\RuntimeListenerProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherFactory
{
    public function __invoke(ContainerInterface $container): EventDispatcherInterface
    {
        return new EventDispatcher([
            $container->get(RuntimeListenerProvider::class)
        ]);
    }
}
