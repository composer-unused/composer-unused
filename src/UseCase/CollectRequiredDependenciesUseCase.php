<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\UseCase;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependency;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependencyInterface;
use Icanhazstring\Composer\Unused\Symbol\Loader\SymbolLoaderInterface;
use Icanhazstring\Composer\Unused\Symbol\SymbolList;

use function dirname;
use function strpos;
use function strtolower;

class CollectRequiredDependenciesUseCase
{
    /** @var SymbolLoaderInterface */
    private $dependencySymbolLoader;

    public function __construct(SymbolLoaderInterface $dependencySymbolLoader)
    {
        $this->dependencySymbolLoader = $dependencySymbolLoader;
    }

    /**
     * @return array<RequiredDependencyInterface>
     */
    public function execute(Composer $composer): array
    {
        $requiredDependencies = [];

        foreach ($composer->getPackage()->getRequires() as $require) {
            $composerPackage = $this->resolveComposerPackage(
                $require,
                $composer->getRepositoryManager()->getLocalRepository()
            );

            if ($composerPackage === null) {
                // TODO handle package filtering
                continue;
            }

            $composerPackage = PackageDecorator::withBaseDir(
                dirname($composer->getConfig()->getConfigSource()->getName()),
                $composerPackage
            );

            $requiredDependencies[] = new RequiredDependency(
                $composerPackage,
                (new SymbolList())->addAll(
                    $this->dependencySymbolLoader->load($composerPackage)
                )
            );
        }

        return $requiredDependencies;
    }

    private function resolveComposerPackage(
        Link $requiredPackage,
        InstalledRepositoryInterface $repo
    ): ?PackageInterface {
        $isPhp = strpos($requiredPackage->getTarget(), 'php') === 0;
        $isExtension = strpos($requiredPackage->getTarget(), 'ext-') === 0;

        if ($isPhp || $isExtension) {
            return new Package(
                strtolower($requiredPackage->getTarget()),
                '*',
                '*'
            );
        }

        $constraint = $requiredPackage->getConstraint();

        if ($constraint === null) {
            $constraint = '';
        }

        return $repo->findPackage($requiredPackage->getTarget(), $constraint);
    }
}
