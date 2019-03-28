<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Loader\Filter;

use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Icanhazstring\Composer\Unused\Loader\Filter\InvalidPackageTypeFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class InvalidPackageTypeFilterTest extends TestCase
{
    public function itShouldValidateFilterDataProvider(): array
    {
        $validPackageType = $this->prophesize(PackageInterface::class);
        $validPackageType->getType()->willReturn('library');

        $validPackageTypeRepository = $this->prophesize(RepositoryInterface::class);
        $validPackageTypeRepository
            ->findPackage(Argument::any(), Argument::any())
            ->willReturn($validPackageType->reveal());

        $invalidPackageType = $this->prophesize(PackageInterface::class);
        $invalidPackageType->getType()->willReturn('composer-plugin');

        $invalidPackageTypeRepository = $this->prophesize(RepositoryInterface::class);
        $invalidPackageTypeRepository
            ->findPackage(Argument::any(), Argument::any())
            ->willReturn($invalidPackageType->reveal());

        return [
            'package with invalid type should match' => [
                'expected'   => true,
                'repository' => $invalidPackageTypeRepository->reveal(),
            ],
            'valid with valid type should not match' => [
                'expected'   => false,
                'repository' => $validPackageTypeRepository->reveal(),
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

        $filter = new InvalidPackageTypeFilter($repository, ['library']);
        $this->assertSame($expected, $filter->match($link->reveal()));
    }
}
