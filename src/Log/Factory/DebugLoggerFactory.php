<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Log\Factory;

use Composer\IO\IOInterface;
use Icanhazstring\Composer\Unused\Log\DebugLogger;
use Icanhazstring\Composer\Unused\Log\LogHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class DebugLoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        /** @var IOInterface $io */
        $io = $container->get(IOInterface::class);

        if ($io->isDebug() === false) {
            return new NullLogger();
        }

        return new DebugLogger($container->get(LogHandlerInterface::class));
    }
}
