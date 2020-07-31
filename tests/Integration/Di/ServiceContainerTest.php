<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Di;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Repository\RepositoryManager;
use Composer\Repository\WritableRepositoryInterface;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Di\ServiceContainer;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Log\LogHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\NamespaceNodeVisitor;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;

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

        $this->assertInstanceOf(NamespaceNodeVisitor::class, $container->get(NamespaceNodeVisitor::class));
        $this->assertInstanceOf(UsageLoader::class, $container->get(UsageLoader::class));
        $this->assertInstanceOf(PackageLoader::class, $container->get(PackageLoader::class));
        $this->assertInstanceOf(PackageSubjectFactory::class, $container->get(PackageSubjectFactory::class));
        $this->assertInstanceOf(ErrorHandlerInterface::class, $container->get(ErrorHandlerInterface::class));
        $this->assertInstanceOf(UnusedCommand::class, $container->get(UnusedCommand::class));
        $this->assertInstanceOf(LoggerInterface::class, $container->get(LoggerInterface::class));
        $this->assertInstanceOf(LogHandlerInterface::class, $container->get(LogHandlerInterface::class));
        $this->assertInstanceOf(LoaderBuilder::class, $container->get(LoaderBuilder::class));
    }
}
