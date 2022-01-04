<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Integration;

use ComposerUnused\ComposerUnused\Console\Command\UnusedCommand;
use ComposerUnused\ComposerUnused\Di\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UnusedCommandTest extends TestCase
{
    /** @var ServiceContainer */
    private $container;

    protected function setUp(): void
    {
        $this->container = require __DIR__ . '/../../config/container.php';
    }

    /**
     * @test
     */
    public function itShouldHaveZeroExitCodeOnEmptyRequirements(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/EmptyRequire');
        $commandTester = new CommandTester($this->container->get(UnusedCommand::class));

        $exitCode = $commandTester->execute([]);

        self::assertSame(0, $exitCode);
    }

    /**
     * @test
     */
    public function itShouldNotReportPHPAsUnused(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/OnlyLanguageRequirement');
        $commandTester = new CommandTester($this->container->get(UnusedCommand::class));

        $exitCode = $commandTester->execute([]);

        self::assertSame(0, $exitCode);
    }

    /**
     * @test
     */
    public function itShouldNotReportExtDsAsUnused(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/ExtDsRequirement');
        $commandTester = new CommandTester($this->container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute([]);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Found 2 used, 0 unused, 0 ignored and 0 zombie packages', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function itShouldNoReportUnusedWithAutoloadFilesWithRequire(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/AutoloadFilesWithRequire');
        $commandTester = new CommandTester($this->container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute([]);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Found 2 used, 0 unused, 0 ignored and 0 zombie packages', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function itShouldNotReportSpecialPackages(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/IgnoreSpecialPackages');
        $commandTester = new CommandTester($this->container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute([]);

        self::assertSame(0, $exitCode);
        self::assertStringNotContainsString('composer-plugin-api', $commandTester->getDisplay());
        self::assertStringContainsString('Found 0 used, 0 unused, 0 ignored and 0 zombie packages', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function itShouldNotReportExcludedPackages(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/IgnoreExcludedPackages');
        $commandTester = new CommandTester($this->container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['--excludePackage' => ['dummy/test-package']]);

        self::assertSame(0, $exitCode);
        self::assertStringNotContainsString('dummy/test-package', $commandTester->getDisplay());
        self::assertStringContainsString('Found 0 used, 0 unused, 0 ignored and 0 zombie packages', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function itShouldNotReportPatternExcludedPackages(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/IgnorePatternPackages');
        $commandTester = new CommandTester($this->container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute([]);

        self::assertSame(1, $exitCode);
        self::assertStringNotContainsString('-implementation', $commandTester->getDisplay());
        self::assertStringContainsString('dummy/test-package', $commandTester->getDisplay());
        self::assertStringContainsString('Found 0 used, 1 unused, 0 ignored and 0 zombie packages', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function itShouldNotReportFileDependencyWithFunctionGuard(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/FileDependencyFunctionWithGuard');
        $commandTester = new CommandTester($this->container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute([]);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Found 1 used, 0 unused, 0 ignored and 0 zombie packages', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function itShouldReportUnusedZombies(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/UnusedZombies');
        $commandTester = new CommandTester($this->container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute([]);

        self::assertSame(1, $exitCode);
        self::assertStringNotContainsString('dummy/test-package', $commandTester->getDisplay());
        self::assertStringContainsString('Found 0 used, 0 unused, 0 ignored and 1 zombie packages', $commandTester->getDisplay());
    }
}
