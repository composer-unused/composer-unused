<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Dependency;

use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Composer\Semver\Constraint\ConstraintInterface;
use Icanhazstring\Composer\Unused\Dependency\Dependency;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependency;
use ComposerUnused\SymbolParser\Symbol\Symbol;
use ComposerUnused\SymbolParser\Symbol\SymbolList;
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

    /**
     * @test
     */
    public function itShouldRequireDependency(): void
    {
        $rootRequirement = new RequiredDependency(
            new Package('root/requirement', '1.0.0', '1.0.0'),
            new SymbolList()
        );

        $requiredPackage = new Package('required/pacakge', '1.0.0', '1.0.0');
        $requiredPackage->setRequires([
            'root/requirement' => new Link('', 'root/requirement', $this->createStub(ConstraintInterface::class))
        ]);

        $requiredDependency = new RequiredDependency($requiredPackage, new SymbolList());

        self::assertTrue($requiredDependency->requires($rootRequirement));
    }

    /**
     * @test
     */
    public function itShouldSuggestDependency(): void
    {
        $rootRequirement = new RequiredDependency(
            new Package('root/requirement', '1.0.0', '1.0.0'),
            new SymbolList()
        );

        $requiredPackage = new Package('required/pacakge', '1.0.0', '1.0.0');
        $requiredPackage->setSuggests(['root/requirement' => '*']);

        $requiredDependency = new RequiredDependency($requiredPackage, new SymbolList());

        self::assertTrue($requiredDependency->suggests($rootRequirement));
    }
}
