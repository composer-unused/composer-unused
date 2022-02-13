<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Integration;

use ComposerUnused\ComposerUnused\Console\Command\UnusedCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Tester\CommandTester;

class UnusedCommandTest extends TestCase
{
    private static ContainerInterface $container;

    public static function setUpBeforeClass(): void
    {
        self::$container = require __DIR__ . '/../../config/container.php';
    }

    /**
     * @test
     */
    public function itShouldHaveZeroExitCodeOnEmptyRequirements(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/EmptyRequire/composer.json']);

        self::assertSame(0, $exitCode);
    }

    /**
     * @test
     */
    public function itShouldNotReportPHPAsUnused(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/OnlyLanguageRequirement/composer.json']);

        self::assertSame(0, $exitCode);
    }

    /**
     * @test
     */
    public function itShouldNotReportExtDsAsUnused(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/ExtDsRequirement/composer.json']);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString(
            'Found 2 used, 0 unused, 0 ignored and 0 zombie packages',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function itShouldNoReportUnusedWithAutoloadFilesWithRequire(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/AutoloadFilesWithRequire/composer.json']);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString(
            'Found 2 used, 0 unused, 0 ignored and 0 zombie packages',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function itShouldNotReportSpecialPackages(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/IgnoreSpecialPackages/composer.json']);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('composer-plugin-api (ignored by NamedFilter', $commandTester->getDisplay());
        self::assertStringContainsString('composer-runtime-api (ignored by NamedFilter', $commandTester->getDisplay());
        self::assertStringContainsString(
            'Found 0 used, 0 unused, 2 ignored and 0 zombie packages',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function itShouldNotReportExcludedPackages(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute([
            'composer-json' => __DIR__ . '/../assets/TestProjects/IgnoreExcludedPackages/composer.json',
            '--excludePackage' => ['dummy/test-package']
        ]);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('dummy/test-package (ignored by NamedFilter', $commandTester->getDisplay());
        self::assertStringContainsString(
            'Found 0 used, 0 unused, 3 ignored and 0 zombie packages',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function itShouldNotReportPatternExcludedPackages(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/IgnorePatternPackages/composer.json']);

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('psr/log-implementation (ignored by PatternFilter', $commandTester->getDisplay());
        self::assertStringContainsString('dummy/ff-implementation (ignored by PatternFilter', $commandTester->getDisplay());
        self::assertStringContainsString('dummy/test-package', $commandTester->getDisplay());
        self::assertStringContainsString(
            'Found 0 used, 1 unused, 2 ignored and 0 zombie packages',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function itShouldNotReportFileDependencyWithFunctionGuard(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/FileDependencyFunctionWithGuard/composer.json']);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString(
            'Found 1 used, 0 unused, 0 ignored and 0 zombie packages',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function itShouldNotReportDependencyWithAdditionalFile(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/DependencyWithAdditionalFile/composer.json']);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString(
            'Found 1 used, 0 unused, 0 ignored and 0 zombie packages',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function itShouldReportUnusedZombies(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/UnusedZombies/composer.json']);

        self::assertSame(1, $exitCode);
        self::assertStringNotContainsString('dummy/test-package', $commandTester->getDisplay());
        self::assertStringContainsString(
            'Found 0 used, 0 unused, 0 ignored and 1 zombie packages',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function itShouldRunWithMultiDependenciesWithClassmap(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/MultiDependencyWithClassmap/composer.json']);

        self::assertSame(0, $exitCode);
        self::assertStringNotContainsString('dummy/test-package', $commandTester->getDisplay());
        self::assertStringContainsString(
            'Found 3 used, 0 unused, 0 ignored and 0 zombie packages',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function itShouldRunWithComposerJsonNotInRoot(): void
    {
        $commandTester = new CommandTester(self::$container->get(UnusedCommand::class));
        $exitCode = $commandTester->execute(['composer-json' => __DIR__ . '/../assets/TestProjects/ComposerJsonNotInRoot/lib/composer.json']);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString(
            'Found 1 used, 0 unused, 0 ignored and 0 zombie packages',
            $commandTester->getDisplay()
        );
    }
}
