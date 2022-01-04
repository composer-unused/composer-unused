<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\Dependency;

use ComposerUnused\Contracts\PackageInterface;
use ComposerUnused\ComposerUnused\Composer\Link;
use ComposerUnused\ComposerUnused\Composer\Package;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
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
            new Package('root/requirement'),
            new SymbolList()
        );

        $requiredPackage = new Package('required/pacakge');
        $requiredPackage->setRequires([
            'root/requirement' => new Link('root/requirement')
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
            new Package('root/requirement'),
            new SymbolList()
        );

        $requiredPackage = new Package('required/pacakge');
        $requiredPackage->setSuggests(['root/requirement']);

        $requiredDependency = new RequiredDependency($requiredPackage, new SymbolList());

        self::assertTrue($requiredDependency->suggests($rootRequirement));
    }
}
