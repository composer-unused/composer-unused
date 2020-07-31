<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\Composer\PackageDecoratorInterface;
use PHPUnit\Framework\TestCase;

use function realpath;

class AbstractIntegrationTestCase extends TestCase
{
    protected function getComposer(string $cwd): Composer
    {
        return (new Factory())->createComposer(new NullIO(), $cwd . '/composer.json', true, $cwd, false);
    }

    protected function loadPackage(string $cwd, string $packageName): PackageDecoratorInterface
    {
        $composer = $this->getComposer($cwd);

        $testDependency = $composer->getPackage()->getRequires()[$packageName];
        $localRepo = $composer->getRepositoryManager()->getLocalRepository();
        /** @var string $constraint */
        $constraint = $testDependency->getConstraint();

        /** @var PackageInterface $package */
        $package = $localRepo->findPackage(
            $testDependency->getTarget(),
            $constraint
        );

        return PackageDecorator::withBaseDir(
            dirname($composer->getConfig()->getConfigSource()->getName()),
            $package
        );
    }
}
