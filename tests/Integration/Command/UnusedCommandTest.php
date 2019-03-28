<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Command;

use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Error\Handler\CollectingErrorHandler;
use Icanhazstring\Composer\Unused\Error\NullDumper;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\Result;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Log\DebugLogger;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use Icanhazstring\Composer\Unused\Parser\Strategy\NewParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\StaticParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\UseParseStrategy;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zend\ServiceManager\ServiceManager;

class UnusedCommandTest extends TestCase
{
    public function itShouldWriteCorrectConsoleOutputDataProvider(): array
    {
        return [
            [
                'usedPackages' => [
                    'package/used-psr0',
                    'package/used-psr4',
                    'package/used-autoload-file',
                    'package/used-classmap-file',
                    'package/used-classmap-folder',
                ],

                'unusedPackages' => [
                    'package/unused-empty-namespace',
                    'package/unused'
                ]
            ]
        ];
    }

    /**
     * @test
     * @param array $usedPackages
     * @param array $unusedPackages
     * @throws \Exception
     * @dataProvider itShouldWriteCorrectConsoleOutputDataProvider
     */
    public function itShouldWriteCorrectConsoleOutput(array $usedPackages, array $unusedPackages): void
    {
        $this->markTestSkipped('Rework with proper integrations tests now');

        chdir(__DIR__ . '/../../assets/TestProject');
        $composer = Factory::create(new NullIO(), 'composer.json');

        $composerIO = $this->prophesize(IOInterface::class);
        $composerIO->isDebug()->willReturn(true);

        $errorHandler = new CollectingErrorHandler();
        $errorDumper = new NullDumper();

        $symfonyStyle = $this->prophesize(SymfonyStyle::class);
        $symfonyStyle->section(Argument::any())->willReturn();
        $symfonyStyle->text(Argument::any())->willReturn();
        $symfonyStyle->writeln(Argument::cetera())->willReturn();
        $symfonyStyle->newLine(Argument::any())->willReturn();
        $symfonyStyle->progressStart(Argument::any())->willReturn();
        $symfonyStyle->progressAdvance()->willReturn();
        $symfonyStyle->progressFinish()->willReturn();

        foreach ($usedPackages as $usedPackage) {
            $symfonyStyle->writeln(Argument::containingString($usedPackage))
                ->shouldBeCalled()
                ->willReturn();
        }

        foreach ($unusedPackages as $unusedPackage) {
            $symfonyStyle->writeln(Argument::containingString($unusedPackage))
                ->shouldBeCalled()
                ->willReturn();
        }

        $symfonyStyleFactory = $this->prophesize(SymfonyStyleFactory::class);
        $symfonyStyleFactory->__invoke(Argument::any(), Argument::any())->willReturn($symfonyStyle->reveal());

        $visitor = new NodeVisitor([
            new NewParseStrategy(),
            new StaticParseStrategy(),
            new UseParseStrategy()
        ]);

        $serviceManager = $this->prophesize(ServiceManager::class);
        $serviceManager->build(PackageLoader::class, ['excludes' => []])->willReturn(
            new PackageLoader(
                $composer->getRepositoryManager()->getLocalRepository(),
                new PackageSubjectFactory(),
                new Result()
            )
        );
        $serviceManager->build(UsageLoader::class, ['excludes' => []])->willReturn(
            new UsageLoader(
                (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
                $visitor,
                $errorHandler,
                new DebugLogger(),
                new Result()
            )
        );

        $command = new UnusedCommand(
            $errorHandler,
            $errorDumper,
            $symfonyStyleFactory->reveal(),
            new LoaderBuilder($serviceManager->reveal()),
            new DebugLogger(),
            $composerIO->reveal()
        );
        $command->setComposer($composer);

        $input = new ArrayInput([]);
        $output = new NullOutput();

        $command->run($input, $output);
    }
}
