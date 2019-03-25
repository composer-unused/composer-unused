<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error\Factory;

use Composer\IO\IOInterface;
use Icanhazstring\Composer\Unused\Error\ErrorDumperInterface;
use Icanhazstring\Composer\Unused\Error\FileDumper;
use Icanhazstring\Composer\Unused\Error\NullDumper;
use Psr\Container\ContainerInterface;

class ErrorDumperFactory
{
    public function __invoke(ContainerInterface $container): ErrorDumperInterface
    {
        /** @var IOInterface $io */
        $io = $container->get(IOInterface::class);

        return $io->isDebug()
            ? $container->get(FileDumper::class)
            : new NullDumper();
    }
}
