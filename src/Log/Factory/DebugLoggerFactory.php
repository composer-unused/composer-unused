<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Log\Factory;

use Icanhazstring\Composer\Unused\Log\DebugLogger;
use Icanhazstring\Composer\Unused\Log\LogHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class DebugLoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        return new DebugLogger($container->get(LogHandlerInterface::class));
    }
}
