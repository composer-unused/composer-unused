<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\OutputFormatter\CompactFormatterTest;

use ComposerUnused\ComposerUnused\Composer\Package;
use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\ComposerUnused\OutputFormatter\CompactFormatter;
use ComposerUnused\ComposerUnused\Test\Stubs\TestDependency;
use ComposerUnused\Contracts\PackageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CompactFormatterTest extends TestCase
{
    private CompactFormatter $compactFormatter;

    protected function setUp(): void
    {
        $this->compactFormatter = new CompactFormatter();
    }


    /**
     * @test
     */
    public function itPrints(): void
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

        $returnStatus = $this->compactFormatter->formatOutput(
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
        self::assertSame('Unused packages: symfony/console', trim($consoleOutput));
    }
}
