<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Factory;

use Icanhazstring\Composer\Unused\Command\UnusedCommandLegacy;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Icanhazstring\Composer\Unused\UseCase\CollectRequiredDependenciesUseCase;
use Icanhazstring\Composer\Unused\UseCase\CollectUsedSymbolsUseCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class UnusedCommandLegacyFactory
{
    public function __invoke(ContainerInterface $container): UnusedCommandLegacy
    {
        return new UnusedCommandLegacy(
            $container->get(ErrorHandlerInterface::class),
            new SymfonyStyleFactory(),
            $container->get(LoaderBuilder::class),
            $container->get(LoggerInterface::class),
            $container->get(CollectUsedSymbolsUseCase::class),
            $container->get(CollectRequiredDependenciesUseCase::class)
        );
    }
}
