<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\OutputFormatter\JsonFormatter;

use ComposerUnused\ComposerUnused\Composer\Package;
use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\ComposerUnused\OutputFormatter\JsonFormatter;
use ComposerUnused\ComposerUnused\OutputFormatter\JUnitFormatter;
use ComposerUnused\ComposerUnused\Test\Stubs\TestDependency;
use ComposerUnused\Contracts\PackageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class JUnitFormatterTest extends TestCase
{
    private JUnitFormatter $junitFormatter;

    protected function setUp(): void
    {
        $this->junitFormatter = new JUnitFormatter();
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

        $this->junitFormatter->formatOutput(
            $this->createMock(PackageInterface::class),
            'composer.json',
            $usedDependencyCollection,
            $unusedDependencyCollection,
            new DependencyCollection(),
            new FilterCollection([], []),
            $outputStyle
        );

        $consoleOutput = $bufferedOutput->fetch() . PHP_EOL;
        self::assertStringEqualsFile(__DIR__ . '/Fixture/expected_unused_packages.xml', $consoleOutput);
    }
}
