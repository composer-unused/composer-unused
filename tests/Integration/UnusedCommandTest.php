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
    /**
     * @test
     */
    public function itShouldHaveZeroExitCodeOnEmptyRequirements(): void
    {
        /** @var ServiceContainer $container */
        $container = require __DIR__ . '/../../config/container.php';

        chdir(__DIR__ . '/../assets/TestProjects/EmptyRequire');

        $application = new Application();
        $application->setAutoExit(false);

        $container->register(IOInterface::class, $application->getIO());
        $container->register(Composer::class, $application->getComposer());

        $application->add($container->get(UnusedCommand::class));

        self::assertEquals(
            0,
            $application->run(
                new ArrayInput(['unused']),
                new NullOutput()
            )
        );
    }
}
