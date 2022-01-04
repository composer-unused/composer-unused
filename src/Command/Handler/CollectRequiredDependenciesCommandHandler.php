<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command\Handler;

use ComposerUnused\SymbolParser\Symbol\SymbolList;
use ComposerUnused\ComposerUnused\Command\LoadRequiredDependenciesCommand;
use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\InvalidDependency;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
use ComposerUnused\ComposerUnused\PackageResolver;
use ComposerUnused\ComposerUnused\Symbol\ProvidedSymbolLoaderBuilder;

final class CollectRequiredDependenciesCommandHandler
{
    /** @var PackageResolver */
    private $packageResolver;
    /** @var ProvidedSymbolLoaderBuilder */
    private $providedSymbolLoaderBuilder;

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
        $providedSymbolLoader = $this->providedSymbolLoaderBuilder->build();

        foreach ($command->getPackageLinks() as $require) {
            $composerPackage = $this->packageResolver->resolve(
                $require,
                $command->getPackageRepository()
            );

            if ($composerPackage === null) {
                $dependencyCollection->add(
                    new InvalidDependency(
                        $require,
                        'Dependency can\'t be located. Maybe not installed?'
                    )
                );
                continue;
            }

            $packageBaseDir = $command->getBaseDir() . DIRECTORY_SEPARATOR . $composerPackage->getName();

            $dependencyCollection->add(
                new RequiredDependency(
                    $composerPackage,
                    (new SymbolList())->addAll(
                        $providedSymbolLoader->withBaseDir($packageBaseDir)->load($composerPackage)
                    )
                )
            );
        }

        return $dependencyCollection;
    }
}
