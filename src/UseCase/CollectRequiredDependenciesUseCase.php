<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\UseCase;

use Composer\Package\Link;
use Composer\Repository\InstalledRepositoryInterface;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\Dependency\DependencyCollection;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependency;
use Icanhazstring\Composer\Unused\PackageResolver;
use Icanhazstring\Composer\Unused\Symbol\Loader\SymbolLoaderInterface;
use Icanhazstring\Composer\Unused\Symbol\SymbolList;

class CollectRequiredDependenciesUseCase
{
    /** @var SymbolLoaderInterface */
    private $dependencySymbolLoader;
    /** @var PackageResolver */
    private $packageResolver;

    public function __construct(SymbolLoaderInterface $dependencySymbolLoader, PackageResolver $packageResolver)
    {
        $this->dependencySymbolLoader = $dependencySymbolLoader;
        $this->packageResolver = $packageResolver;
    }

    /**
     * @param array<Link> $packageLinks
     * @return DependencyCollection
     */
    public function execute(
        array $packageLinks,
        InstalledRepositoryInterface $repository,
        string $composerBaseDir
    ): DependencyCollection {
        $dependencyCollection = new DependencyCollection();

        foreach ($packageLinks as $require) {
            $composerPackage = $this->packageResolver->resolve(
                $require,
                $repository
            );

            if ($composerPackage === null) {
                // TODO handle package filtering
                continue;
            }

            $composerPackage = PackageDecorator::withBaseDir(
                $composerBaseDir,
                $composerPackage
            );

            $dependencyCollection->add(
                new RequiredDependency(
                    $composerPackage,
                    (new SymbolList())->addAll(
                        $this->dependencySymbolLoader->load($composerPackage)
                    )
                )
            );
        }

        return $dependencyCollection;
    }
}
