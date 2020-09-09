<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Loader;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Package\RootPackage;
use Composer\Repository\ArrayRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Semver\VersionParser;
use Icanhazstring\Composer\Unused\Loader\Filter\ExcludePackageFilter;
use Icanhazstring\Composer\Unused\Loader\Filter\InvalidNamespaceFilter;
use Icanhazstring\Composer\Unused\Loader\Filter\NullPackageFilter;
use Icanhazstring\Composer\Unused\Loader\PackageHelper;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\Result;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class PackageLoaderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function itShouldSkipPackageThatProvidesNoNamespace(): void
    {
        $parser = new VersionParser();
        $constraint = $parser->parseConstraints('*');
        $require = new Link('', 'package/a', $constraint);

        $rootPackage = new RootPackage('Rootpackage', '0.1', '0.1');
        $rootPackage->setRequires([$require]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage);

        $package = new Package('package/a', '0.1', '0.1');
        $package->setAutoload([]);
        $package->setDevAutoload([]);

        $packageRepository = new ArrayRepository([$package]);

        $result = $this->prophesize(Result::class);
        $result->skipItem(
            'package/a',
            'Package provides no namespace'
        )->shouldBeCalled()->willReturn($result->reveal());
        $result->getItems()->willReturn([]);

        $loader = new PackageLoader(
            $packageRepository,
            new PackageSubjectFactory(),
            $result->reveal(),
            new PackageHelper(),
            [
                new InvalidNamespaceFilter($packageRepository)
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
        $parser = new VersionParser();
        $constraint = $parser->parseConstraints('*');
        $require = new Link('', 'package/a', $constraint);

        $rootPackage = new RootPackage('Rootpackage', '0.1', '0.1');
        $rootPackage->setRequires([$require]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage);

        $packageRepository = new ArrayRepository([$rootPackage]);

        $result = $this->prophesize(Result::class);
        $result->skipItem('package/a', 'Unable to locate package')->shouldBeCalled()->willReturn($result->reveal());
        $result->getItems()->willReturn([]);

        $loader = new PackageLoader(
            $packageRepository,
            new PackageSubjectFactory(),
            $result->reveal(),
            new PackageHelper(),
            [
                new NullPackageFilter($packageRepository, new PackageHelper())
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
        $rootPackage = new RootPackage('Rootpackage', '0.1', '0.1');
        $rootPackage->setRequires([]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage);

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

        self::assertSame($result, $loaderResult);
        self::assertEmpty($loaderResult->getItems());
    }

    /**
     * @test
     */
    public function itShouldReturnLoaderResultWhenPackagesAreEmptyWhenFiltered(): void
    {
        $parser = new VersionParser();
        $constraint = $parser->parseConstraints('*');
        $require = new Link('', 'package/a', $constraint);

        $rootPackage = new RootPackage('Rootpackage', '0.1', '0.1');
        $rootPackage->setRequires([$require]);

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn($rootPackage);

        $package = new Package('package/a', '0.1', '0.1');
        $package->setAutoload(['psr-4' => ['ABC\\' => 'src']]);
        $package->setDevAutoload([]);

        $packageRepository = new ArrayRepository([$package]);
        $result = new Result();

        $loader = new PackageLoader(
            $packageRepository,
            new PackageSubjectFactory(),
            $result,
            new PackageHelper(),
            [
                new ExcludePackageFilter(['package/a'])
            ]
        );

        $loaderResult = $loader->load(
            $composer->reveal(),
            new SymfonyStyle(new ArrayInput([]), new NullOutput())
        );

        self::assertEmpty($loaderResult->getItems(), 'All packages should be filtered');
    }
}
