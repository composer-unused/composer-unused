<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\OutputFormatter\NoneFormatterTest;

use ComposerUnused\ComposerUnused\Composer\Package;
use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\ComposerUnused\OutputFormatter\NoneFormatter;
use ComposerUnused\ComposerUnused\Test\Stubs\TestDependency;
use ComposerUnused\Contracts\PackageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class NoneFormatterTest extends TestCase
{
    private NoneFormatter $noneFormatter;

    protected function setUp(): void
    {
        $this->noneFormatter = new NoneFormatter();
    }


    /**
     * @test
     */
    public function itPrintsNothing(): void
    {
        $symfonyStringRequiredDependency = new RequiredDependency(
            new Package('symfony/string'),
        );
        $symfonyStringRequiredDependency->requiredBy(new TestDependency('symfony/event-dispatcher'));
        $usedDependencyCollection = new DependencyCollection([$symfonyStringRequiredDependency]);

        $unusedDependencyCollection = new DependencyCollection([
            new RequiredDependency(new Package('symfony/console'))
        ]);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new SymfonyStyle(new ArgvInput(), $bufferedOutput);

        $returnStatus = $this->noneFormatter->formatOutput(
            $this->createMock(PackageInterface::class),
            'composer.json',
            $usedDependencyCollection,
            $unusedDependencyCollection,
            new DependencyCollection(),
            new FilterCollection([], []),
            $outputStyle
        );
        $consoleOutput = $bufferedOutput->fetch();

        self::assertSame(1, $returnStatus);
        self::assertEmpty($consoleOutput);
    }
}
