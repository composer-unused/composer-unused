<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Loader\Filter;

use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Icanhazstring\Composer\Unused\Loader\Filter\NullPackageFilter;
use Icanhazstring\Composer\Unused\Loader\PackageHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class NullPackageFilterTest extends TestCase
{
    public function itShouldValidateFilterDataProvider(): array
    {
        $package = $this->prophesize(PackageInterface::class);

        $validRepository = $this->prophesize(RepositoryInterface::class);
        $validRepository->findPackage(Argument::any(), Argument::any())->willReturn($package->reveal());

        $invalidRepository = $this->prophesize(RepositoryInterface::class);
        $invalidRepository->findPackage(Argument::any(), Argument::any())->willReturn(null);

        return [
            'found package should not match' => [
                'expected'   => false,
                'repository' => $validRepository->reveal(),
            ],
            'not found package should match' => [
                'expected'   => true,
                'repository' => $invalidRepository->reveal()
            ]
        ];
    }

    /**
     * @param bool                $expected
     * @param RepositoryInterface $repository
     * @return void
     * @test
     * @dataProvider itShouldValidateFilterDataProvider
     */
    public function itShouldValidateFilter(bool $expected, RepositoryInterface $repository): void
    {
        // Link is unimportant here
        $link = $this->prophesize(Link::class);
        $link->getTarget()->willReturn();
        $link->getConstraint()->willReturn();

        $filter = new NullPackageFilter($repository, new PackageHelper());
        $this->assertSame($expected, $filter->match($link->reveal()));
    }
}
