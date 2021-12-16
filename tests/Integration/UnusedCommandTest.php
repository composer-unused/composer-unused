<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration;

use Icanhazstring\Composer\Unused\Console\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Di\ServiceContainer;
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
        self::assertStringContainsString('Found 2 used, 0 unused and 0 ignored packages', $commandTester->getDisplay());
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
        self::assertStringContainsString('Found 2 used, 0 unused and 0 ignored packages', $commandTester->getDisplay());
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
        self::assertStringContainsString('Found 0 used, 0 unused and 0 ignored packages', $commandTester->getDisplay());
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
        self::assertStringContainsString('Found 0 used, 0 unused and 0 ignored packages', $commandTester->getDisplay());
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
        self::assertStringContainsString('Found 0 used, 1 unused and 0 ignored packages', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function itShouldNotReportFileDependencyWithFunctionGuard(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/FileDependencyFunctionWithGuard');

        self::assertEquals(
            0,
            $this->getApplication()->run(
                new ArrayInput(['unused']),
                new NullOutput()
            )
        );
    }
}
