<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command\Handler;

use ComposerUnused\ComposerUnused\Composer\InvalidPackage;
use ComposerUnused\SymbolParser\Symbol\SymbolList;
use ComposerUnused\ComposerUnused\Command\LoadRequiredDependenciesCommand;
use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
use ComposerUnused\ComposerUnused\PackageResolver;
use ComposerUnused\ComposerUnused\Symbol\ProvidedSymbolLoaderBuilder;

final class CollectRequiredDependenciesCommandHandler
{
    private PackageResolver $packageResolver;
    private ProvidedSymbolLoaderBuilder $providedSymbolLoaderBuilder;

    public function __construct(
        PackageResolver $packageResolver,
        ProvidedSymbolLoaderBuilder $providedSymbolLoaderBuilder
    ) {
        $this->packageResolver = $packageResolver;
        $this->providedSymbolLoaderBuilder = $providedSymbolLoaderBuilder;
    }

    public function collect(LoadRequiredDependenciesCommand $command): DependencyCollection
    {
        $dependencyCollection = new DependencyCollection();

        $progressBar = $command->getProgressBar();

        $progressBar->start();

        foreach ($command->getPackageLinks() as $require) {
            $composerPackage = $this->packageResolver->resolve(
                $require,
                $command->getPackageRepository()
            );

            if ($composerPackage === null) {
                $invalidDependency = new RequiredDependency(new InvalidPackage($require->getTarget()));
                $invalidDependency->markIgnored('Dependency can\'t be located. Maybe not installed?');

                $dependencyCollection->add($invalidDependency);
                continue;
            }

            $packageBaseDir = $command->getBaseDir() . DIRECTORY_SEPARATOR . $composerPackage->getName();

            $providedSymbolLoader = $this
                ->providedSymbolLoaderBuilder
                ->setAdditionalFiles($command->getConfiguration()->getAdditionalFilesFor($composerPackage->getName()))
                ->build();

            $dependencyCollection->add(
                new RequiredDependency(
                    $composerPackage,
                    (new SymbolList())->addAll(
                        $providedSymbolLoader->withBaseDir($packageBaseDir)->load($composerPackage)
                    )
                )
            );
            $progressBar->advance();
        }

        $progressBar->finish();

        return $dependencyCollection;
    }
}
