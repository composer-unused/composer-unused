<?php

declare(strict_types=1);

use Icanhazstring\Composer\Unused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use Icanhazstring\Composer\Unused\Command\Handler\CollectConsumedSymbolsCommandHandlerFactory;
use Icanhazstring\Composer\Unused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use Icanhazstring\Composer\Unused\Command\Handler\CollectRequiredDependenciesCommandHandlerFactory;
use Icanhazstring\Composer\Unused\Command\Handler\ConsumedSymbolLoaderBuilder;
use Icanhazstring\Composer\Unused\Command\Handler\ProvidedSymbolLoaderBuilder;
use Icanhazstring\Composer\Unused\Console\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Console\Command\UnusedCommandFactory;
use Icanhazstring\Composer\Unused\Di\InvokableFactory;
use Icanhazstring\Composer\Unused\PackageResolver;

return [
    'factories' => [
        UnusedCommand::class => UnusedCommandFactory::class,
        CollectConsumedSymbolsCommandHandler::class => CollectConsumedSymbolsCommandHandlerFactory::class,
        CollectRequiredDependenciesCommandHandler::class => CollectRequiredDependenciesCommandHandlerFactory::class,
        ConsumedSymbolLoaderBuilder::class => InvokableFactory::class,
        ProvidedSymbolLoaderBuilder::class => InvokableFactory::class,

        PackageResolver::class => InvokableFactory::class,
    ]
];
