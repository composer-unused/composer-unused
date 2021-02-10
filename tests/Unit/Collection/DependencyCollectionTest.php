<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Collection;

use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Dependency\DependencyCollection;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependency;
use ComposerUnused\SymbolParser\Symbol\SymbolListInterface;
use PHPUnit\Framework\TestCase;

final class DependencyCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldPartitionItems(): void
    {
        $package = $this->createStub(PackageInterface::class);
        $symbolList = $this->createStub(SymbolListInterface::class);

        $usedDependency = new RequiredDependency($package, $symbolList);
        $usedDependency->markUsed();

        $unusedDependency1 = new RequiredDependency($package, $symbolList);
        $unusedDependency2 = new RequiredDependency($package, $symbolList);

        $collection = new DependencyCollection([$usedDependency, $unusedDependency1, $unusedDependency2]);

        [$usedDependencyCollection, $unusedDependencyCollection] = $collection->partition(
            static function (RequiredDependency $dependency) {
                return $dependency->inState($dependency::STATE_USED);
            }
        );

        self::assertCount(3, $collection);
        self::assertCount(1, $usedDependencyCollection);
        self::assertCount(2, $unusedDependencyCollection);
    }
}
