<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration;

use Composer\Composer;
use Composer\Console\Application;
use Composer\IO\IOInterface;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Di\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class UnusedCommandTest extends TestCase
{
    /** @var ServiceContainer */
    private $container;

    protected function setUp(): void
    {
        /** @var ServiceContainer $container */
        $this->container = require __DIR__ . '/../../config/container.php';
    }

    private function getApplication(): Application
    {
        $application = new Application();
        $application->setAutoExit(false);

        $this->container->register(IOInterface::class, $application->getIO());
        $this->container->register(Composer::class, $application->getComposer());
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
}
