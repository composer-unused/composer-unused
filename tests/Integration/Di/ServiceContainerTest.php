<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Di;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Repository\RepositoryManager;
use Composer\Repository\WritableRepositoryInterface;
use Icanhazstring\Composer\Unused\Di\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function gettype;

class ServiceContainerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function itShouldLoadServiceContainer(): void
    {
        /** @var ServiceContainer $container */
        $container = require __DIR__ . '/../../../config/container.php';
        $container->register(IOInterface::class, $this->prophesize(IOInterface::class)->reveal());
        $repositoryManager = $this->prophesize(RepositoryManager::class);
        $repositoryManager->getLocalRepository()->willReturn($this->prophesize(WritableRepositoryInterface::class)->reveal());
        $composer = $this->prophesize(Composer::class);
        $composer->getRepositoryManager()->willReturn($repositoryManager->reveal());
        $container->register(Composer::class, $composer->reveal());

        $services = require __DIR__ . '/../../../config/service_manager.php';

        foreach ($services['factories'] as $type => $factory) {
            self::assertInstanceOf($type, $container->get($type));
        }
    }
}
