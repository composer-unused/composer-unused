<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Loader\Filter;

use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Icanhazstring\Composer\Unused\Loader\Filter\InvalidNamespaceFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class InvalidNamespaceFilterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function itShouldValidateFilterDataProvider(): array
    {
        $validNamespacePackage = $this->prophesize(PackageInterface::class);
        $validNamespacePackage->getAutoload()->willReturn(['psr-4' => ['A\\' => 'src']]);
        $validNamespacePackage->getDevAutoload()->willReturn([]);

        $validNamespaceRepository = $this->prophesize(RepositoryInterface::class);
        $validNamespaceRepository
            ->findPackage(Argument::any(), Argument::any())
            ->willReturn($validNamespacePackage->reveal());

        $emptyNamespacePackage = $this->prophesize(PackageInterface::class);
        $emptyNamespacePackage->getAutoload()->willReturn(['psr-4' => ['' => ['src']]]);
        $emptyNamespacePackage->getDevAutoload()->willReturn([]);

        $emptyNamespaceRepository = $this->prophesize(RepositoryInterface::class);
        $emptyNamespaceRepository
            ->findPackage(Argument::any(), Argument::any())
            ->willReturn($emptyNamespacePackage->reveal());

        $mixedNamespacePackage = $this->prophesize(PackageInterface::class);
        $mixedNamespacePackage->getAutoload()->willReturn(['psr-4' => ['' => 'src', 'A\\' => 'src']]);
        $mixedNamespacePackage->getDevAutoload()->willReturn([]);

        $mixedNamespaceRepository = $this->prophesize(RepositoryInterface::class);
        $mixedNamespaceRepository
            ->findPackage(Argument::any(), Argument::any())
            ->willReturn($mixedNamespacePackage->reveal());

        return [
            'package with valid namespace should not match'           => [
                'expected'   => false,
                'repository' => $validNamespaceRepository->reveal(),
            ],
            'package with empty namespace should match'               => [
                'expected'   => true,
                'repository' => $emptyNamespaceRepository->reveal()
            ],
            'package with empty and valid namespace should not match' => [
                'expected'   => false,
                'repository' => $mixedNamespaceRepository->reveal()
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

        $filter = new InvalidNamespaceFilter($repository);
        $this->assertSame($expected, $filter->match($link->reveal()));
    }
}
