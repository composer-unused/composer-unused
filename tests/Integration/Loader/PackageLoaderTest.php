<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Loader;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Repository\RepositoryInterface;
use Icanhazstring\Composer\Unused\Loader\Filter\ExcludePackageFilter;
use Icanhazstring\Composer\Unused\Loader\Filter\InvalidNamespaceFilter;
use Icanhazstring\Composer\Unused\Loader\Filter\NullConstraintFilter;
use Icanhazstring\Composer\Unused\Loader\Filter\NullPackageFilter;
use Icanhazstring\Composer\Unused\Loader\PackageHelper;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\Result;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class PackageLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldSkipPackageThatProvidesNoNamespace(): void
    {
        $link = $this->prophesize(Link::class);
        $link->getTarget()->willReturn('package/A');
        $link->getConstraint()->willReturn('^0.1');

        $rootPackage = $this->prophesize(RootPackageInterface::class);
        $rootPackage->getRequires()->willReturn([$link->reveal()]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage->reveal());

        $package = $this->prophesize(PackageInterface::class);
        $package->getAutoload()->willReturn([]);
        $package->getDevAutoload()->willReturn([]);

        $packageRepository = $this->prophesize(RepositoryInterface::class);
        $packageRepository->findPackage('package/A', '^0.1')->willReturn($package->reveal());

        $result = $this->prophesize(Result::class);
        $result->skipItem('package/A', 'Package provides no namespace')->shouldBeCalled()->willReturn($result->reveal());
        $result->getItems()->willReturn([]);

        $loader = new PackageLoader(
            $packageRepository->reveal(),
            new PackageSubjectFactory(),
            $result->reveal(),
            new PackageHelper(),
            [
                new InvalidNamespaceFilter($packageRepository->reveal())
            ]
        );

        $loader->load(
            $composer->reveal(),
            new SymfonyStyle(new ArrayInput([]), new NullOutput())
        );
    }

    /**
     * @test
     */
    public function itShouldSkipPackageThatWasNotFound(): void
    {
        $link = $this->prophesize(Link::class);
        $link->getTarget()->willReturn('package/A');
        $link->getConstraint()->willReturn('^0.1');

        $rootPackage = $this->prophesize(RootPackageInterface::class);
        $rootPackage->getRequires()->willReturn([$link->reveal()]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage->reveal());

        $packageRepository = $this->prophesize(RepositoryInterface::class);
        $packageRepository->findPackage('package/A', '^0.1')->willReturn(null);

        $result = $this->prophesize(Result::class);
        $result->skipItem('package/A', 'Unable to locate package')->shouldBeCalled()->willReturn($result->reveal());
        $result->getItems()->willReturn([]);

        $loader = new PackageLoader(
            $packageRepository->reveal(),
            new PackageSubjectFactory(),
            $result->reveal(),
            new PackageHelper(),
            [
                new NullPackageFilter($packageRepository->reveal(), new PackageHelper())
            ]
        );

        $loader->load(
            $composer->reveal(),
            new SymfonyStyle(new ArrayInput([]), new NullOutput())
        );
    }

    /**
     * @test
     */
    public function itShouldSkipPackageWithInvalidConstraint(): void
    {
        $link = $this->prophesize(Link::class);
        $link->getTarget()->willReturn('package/A');
        $link->getConstraint()->willReturn(null);

        $rootPackage = $this->prophesize(RootPackageInterface::class);
        $rootPackage->getRequires()->willReturn([$link->reveal()]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage->reveal());

        $packageRepository = $this->prophesize(RepositoryInterface::class);

        $result = $this->prophesize(Result::class);
        $result->skipItem('package/A', 'Invalid constraint')->shouldBeCalled()->willReturn($result->reveal());
        $result->getItems()->willReturn([]);

        $loader = new PackageLoader(
            $packageRepository->reveal(),
            new PackageSubjectFactory(),
            $result->reveal(),
            new PackageHelper(),
            [
                new NullConstraintFilter()
            ]
        );

        $loader->load(
            $composer->reveal(),
            new SymfonyStyle(new ArrayInput([]), new NullOutput())
        );
    }

    /**
     * @test
     */
    public function itShouldReturnLoaderResultWhenPackagesAreEmpty(): void
    {
        $rootPackage = $this->prophesize(RootPackageInterface::class);
        $rootPackage->getRequires()->willReturn([]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage->reveal());

        $packageRepository = $this->prophesize(RepositoryInterface::class);
        $result = new Result();

        $loader = new PackageLoader(
            $packageRepository->reveal(),
            new PackageSubjectFactory(),
            $result,
            new PackageHelper(),
            []
        );

        $loaderResult = $loader->load(
            $composer->reveal(),
            new SymfonyStyle(new ArrayInput([]), new NullOutput())
        );

        $this->assertSame($result, $loaderResult);
        $this->assertEmpty($loaderResult->getItems());
    }

    /**
     * @test
     */
    public function itShouldReturnLoaderResultWhenPackagesAreEmptyWhenFiltered(): void
    {
        $link = $this->prophesize(Link::class);
        $link->getTarget()->willReturn('package/A');

        $rootPackage = $this->prophesize(RootPackageInterface::class);
        $rootPackage->getRequires()->willReturn([$link->reveal()]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage->reveal());

        $package = $this->prophesize(PackageInterface::class);
        $package->getAutoload()->willReturn(['psr-4' => ['ABC\\' => 'src']]);
        $package->getDevAutoload()->willReturn([]);

        $packageRepository = $this->prophesize(RepositoryInterface::class);
        $packageRepository->findPackage('package/A', '^0.1')->willReturn($package->reveal());

        $result = new Result();

        $loader = new PackageLoader(
            $packageRepository->reveal(),
            new PackageSubjectFactory(),
            $result,
            new PackageHelper(),
            [
                new ExcludePackageFilter(['package/A'])
            ]
        );

        $loaderResult = $loader->load(
            $composer->reveal(),
            new SymfonyStyle(new ArrayInput([]), new NullOutput())
        );

        $this->assertEmpty($loaderResult->getItems(), 'All packages should be filtered');
    }
}
