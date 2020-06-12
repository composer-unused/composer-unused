<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Dependency;

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
        $dependencyStub = $this->getMockForAbstractClass(DependencyInterface::class);
        $requiredDependency = new RequiredDependency($dependencyStub);

        self::assertFalse($requiredDependency->isUsed());
        $requiredDependency->markUsed();

        self::assertTrue($requiredDependency->isUsed());
    }

    /**
     * @test
     */
    public function itShouldProvideSymbol(): void
    {
        $symbol = new Symbol('test');
        $dependency = new Dependency(
            (new SymbolList())->add($symbol),
            new SymbolList(),
            new SymbolList()
        );

        $requiredDependency = new RequiredDependency($dependency);
        self::assertTrue($requiredDependency->provides(new Symbol('test')));
    }
}
