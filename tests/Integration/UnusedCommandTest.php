<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration;

use Composer\Composer;
use Composer\Console\Application;
use Composer\IO\IOInterface;
use Icanhazstring\Composer\Unused\Command\UnusedCommandLegacy;
use Icanhazstring\Composer\Unused\Console\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Di\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;

class UnusedCommandTest extends TestCase
{
    /** @var ServiceContainer */
    private $container;

    protected function setUp(): void
    {
        $this->container = require __DIR__ . '/../../config/container.php';
    }

    private function getApplication(): Application
    {
        $application = new Application();
        $application->setAutoExit(false);

        $io = $application->getIO();
        /** @var Composer $composer */
        $composer = $application->getComposer();

        $this->container->register(IOInterface::class, $io);
        $this->container->register(Composer::class, $composer);
        $application->add($this->container->get(UnusedCommand::class));

        return $application;
    }

    /**
     * @test
     */
    public function itShouldHaveZeroExitCodeOnEmptyRequirements(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/EmptyRequire');

        self::assertEquals(
            0,
            $this->getApplication()->run(
                new ArrayInput(['unused']),
                new NullOutput()
            )
        );
    }

    /**
     * @test
     */
    public function itShouldNotReportPHPAsUnused(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/OnlyLanguageRequirement');

        self::assertEquals(
            0,
            $this->getApplication()->run(
                new ArrayInput(['unused']),
                new NullOutput()
            )
        );
    }

    /**
     * @test
     */
    public function itShouldNotReportExtDsAsUnused(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/ExtDsRequirement');

        self::assertEquals(
            0,
            $this->getApplication()->run(
                new ArrayInput(['unused']),
                new NullOutput()
            )
        );
    }

    /**
     * @test
     */
    public function itShouldNoReportUnusedWithAutoloadFilesWithRequire(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/AutoloadFilesWithRequire');

        self::assertEquals(
            0,
            $this->getApplication()->run(
                new ArrayInput(['unused']),
                new NullOutput()
            )
        );
    }

    /**
     * @test
     */
    public function itShouldNotReportSpecialPackages(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/IgnoreSpecialPackages');

        $output = new BufferedOutput();

        $this->getApplication()->run(
            new ArrayInput(['unused']),
            $output
        );

        self::assertStringNotContainsString('composer-plugin-api', $output->fetch());
    }

    /**
     * @test
     */
    public function itShouldNotReportExcludedPackages(): void
    {
        chdir(__DIR__ . '/../assets/TestProjects/IgnoreExcludedPackages');

        $output = new BufferedOutput();

        $this->getApplication()->run(
            new ArrayInput(['unused', '--excludePackage' => ['dummy/test-package']]),
            $output
        );

        self::assertStringNotContainsString('dummy/test-package', $output->fetch());
    }
}
