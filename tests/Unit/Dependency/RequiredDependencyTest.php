<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Dependency;

use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Dependency\Dependency;
use Icanhazstring\Composer\Unused\Dependency\DependencyInterface;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependency;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolList;
use PHPUnit\Framework\TestCase;

class RequiredDependencyTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldMarkAsUsed(): void
    {
        $package = $this->getMockForAbstractClass(PackageInterface::class);
        $requiredDependency = new RequiredDependency($package, new SymbolList());

        self::assertFalse($requiredDependency->inState($requiredDependency::STATE_USED));
        $requiredDependency->markUsed();

        self::assertTrue($requiredDependency->inState($requiredDependency::STATE_USED));
    }

    /**
     * @test
     */
    public function itShouldProvideSymbol(): void
    {
        $symbol = new Symbol('test');

        $package = $this->getMockForAbstractClass(PackageInterface::class);
        $requiredDependency = new RequiredDependency($package, (new SymbolList())->add($symbol));

        self::assertTrue($requiredDependency->provides(new Symbol('test')));
    }
}
