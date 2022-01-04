<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use ComposerUnused\ComposerUnused\Command\Handler\CollectFilteredDependenciesCommandHandler;
use ComposerUnused\ComposerUnused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use ComposerUnused\ComposerUnused\Command\Handler\Factory\CollectConsumedSymbolsCommandHandlerFactory;
use ComposerUnused\ComposerUnused\Command\Handler\Factory\CollectFilteredDependenciesCommandHandlerFactory;
use ComposerUnused\ComposerUnused\Command\Handler\Factory\CollectRequiredDependenciesCommandHandlerFactory;
use ComposerUnused\ComposerUnused\Composer\ConfigFactory;
use ComposerUnused\ComposerUnused\Console\Command\UnusedCommand;
use ComposerUnused\ComposerUnused\Console\Command\UnusedCommandFactory;
use ComposerUnused\ComposerUnused\Di\InvokableFactory;
use ComposerUnused\ComposerUnused\PackageResolver;
use ComposerUnused\ComposerUnused\Symbol\ConsumedSymbolLoaderBuilder;
use ComposerUnused\ComposerUnused\Symbol\ProvidedSymbolLoaderBuilder;

return [
    'factories' => [
        UnusedCommand::class => UnusedCommandFactory::class,
        CollectConsumedSymbolsCommandHandler::class => CollectConsumedSymbolsCommandHandlerFactory::class,
        CollectRequiredDependenciesCommandHandler::class => CollectRequiredDependenciesCommandHandlerFactory::class,
        CollectFilteredDependenciesCommandHandler::class => CollectFilteredDependenciesCommandHandlerFactory::class,
        ConsumedSymbolLoaderBuilder::class => InvokableFactory::class,
        ProvidedSymbolLoaderBuilder::class => InvokableFactory::class,
        PackageResolver::class => InvokableFactory::class,
        ConfigFactory::class => InvokableFactory::class,
    ]
];
