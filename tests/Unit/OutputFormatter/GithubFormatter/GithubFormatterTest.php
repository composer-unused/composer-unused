<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\OutputFormatter\GithubFormatter;

use ComposerUnused\ComposerUnused\Composer\Package;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;
use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\ComposerUnused\OutputFormatter\GithubFormatter;
use ComposerUnused\ComposerUnused\OutputFormatter\JsonFormatter;
use ComposerUnused\ComposerUnused\Test\Stubs\TestDependency;
use ComposerUnused\Contracts\PackageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GithubFormatterTest extends TestCase
{
    private GithubFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new GithubFormatter();
    }

    /**
     * @test
     */
    public function itPrints(): void
    {
        $usedCollection = new DependencyCollection([new RequiredDependency(new Package('symfony/string'))]);
        $unusedCollection = new DependencyCollection([new RequiredDependency(new Package('symfony/console'))]);
        $ignoredCollection = new DependencyCollection([new RequiredDependency(new Package('symfony/ignored'))]);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new SymfonyStyle(new ArgvInput(), $bufferedOutput);

        $this->formatter->formatOutput(
            $this->createMock(PackageInterface::class),
            'composer.json',
            $usedCollection,
            $unusedCollection,
            $ignoredCollection,
            new FilterCollection([NamedFilter::fromString('symfony/zombie')], []),
            $outputStyle
        );

        $consoleOutput = $bufferedOutput->fetch() . PHP_EOL;
        self::assertStringEqualsFile(__DIR__ . '/Fixture/expected_unused_packages.txt', $consoleOutput);
    }
}
