<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Handler;

use ComposerUnused\SymbolParser\Symbol\SymbolList;
use Icanhazstring\Composer\Unused\Command\LoadRequiredDependenciesCommand;
use Icanhazstring\Composer\Unused\Dependency\DependencyCollection;
use Icanhazstring\Composer\Unused\Dependency\InvalidDependency;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependency;
use Icanhazstring\Composer\Unused\PackageResolver;
use Icanhazstring\Composer\Unused\Symbol\ProvidedSymbolLoaderBuilder;

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
        $providedSymbolLoader = $this->providedSymbolLoaderBuilder->build($command->getBaseDir());

        foreach ($command->getPackageLinks() as $require) {
            $composerPackage = $this->packageResolver->resolve(
                $require,
                $command->getPackageRepository()
            );

            if ($composerPackage === null) {
                $dependencyCollection->add(new InvalidDependency($require, 'Unable to locate package'));
                continue;
            }

            $dependencyCollection->add(
                new RequiredDependency(
                    $composerPackage,
                    (new SymbolList())->addAll(
                        $providedSymbolLoader->load($composerPackage)
                    )
                )
            );
        }

        return $dependencyCollection;
    }
}
