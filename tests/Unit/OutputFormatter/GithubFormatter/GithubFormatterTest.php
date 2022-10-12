<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\OutputFormatter\GithubFormatter;

use ComposerUnused\ComposerUnused\Composer\Package;
use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\ComposerUnused\OutputFormatter\GithubFormatter;
use ComposerUnused\ComposerUnused\Test\Stubs\TestDependency;
use ComposerUnused\Contracts\PackageInterface;
use PHPStan\Testing\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GithubFormatterTest extends TestCase
{
    private GithubFormatter $githubFormatter;

    protected function setUp(): void
    {
        $this->githubFormatter = new GithubFormatter();
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

        $this->githubFormatter->formatOutput(
            $this->createMock(PackageInterface::class),
            'composer.json',
            $usedDependencyCollection,
            $unusedDependencyCollection,
            $this->createMock(DependencyCollection::class),
            $this->createMock(FilterCollection::class),
            $outputStyle
        );

        $consoleOutput = $bufferedOutput->fetch() . PHP_EOL;
        self::assertStringEqualsFile(__DIR__ . '/expected_unused_packages.json', $consoleOutput);
    }
}