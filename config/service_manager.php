<?php

declare(strict_types=1);

use Icanhazstring\Composer\Unused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use Icanhazstring\Composer\Unused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use Icanhazstring\Composer\Unused\Command\Handler\Factory\CollectConsumedSymbolsCommandHandlerFactory;
use Icanhazstring\Composer\Unused\Command\Handler\Factory\CollectRequiredDependenciesCommandHandlerFactory;
use Icanhazstring\Composer\Unused\Console\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Console\Command\UnusedCommandFactory;
use Icanhazstring\Composer\Unused\Di\InvokableFactory;
use Icanhazstring\Composer\Unused\PackageResolver;
use Icanhazstring\Composer\Unused\Symbol\ConsumedSymbolLoaderBuilder;
use Icanhazstring\Composer\Unused\Symbol\ProvidedSymbolLoaderBuilder;

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
