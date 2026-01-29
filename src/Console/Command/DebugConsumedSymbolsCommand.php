<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Console\Command;

use ComposerUnused\ComposerUnused\Command\CollectConsumedSymbolsCommand;
use ComposerUnused\ComposerUnused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use ComposerUnused\ComposerUnused\Composer\ConfigFactory;
use ComposerUnused\ComposerUnused\Composer\PackageFactory;
use ComposerUnused\ComposerUnused\Configuration\ConfigurationProvider;
use ComposerUnused\SymbolParser\Symbol\SymbolInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'debug:consumed-symbols')]
final class DebugConsumedSymbolsCommand extends Command
{
    private CollectConsumedSymbolsCommandHandler $collectConsumedSymbolsCommandHandler;
    private PackageFactory $packageFactory;
    private ConfigFactory $configFactory;
    private ConfigurationProvider $configurationProvider;

    public function __construct(
        ConfigFactory $configFactory,
        CollectConsumedSymbolsCommandHandler $collectConsumedSymbolsCommandHandler,
        PackageFactory $packageFactory,
        ConfigurationProvider $configurationProvider
    ) {
        parent::__construct();
        $this->collectConsumedSymbolsCommandHandler = $collectConsumedSymbolsCommandHandler;
        $this->packageFactory = $packageFactory;
        $this->configFactory = $configFactory;
        $this->configurationProvider = $configurationProvider;
    }

    protected function configure(): void
    {
        $this->setDescription('List all consumed symbols from the root package.');

        $this->addOption(
            'composer-json',
            null,
            InputOption::VALUE_REQUIRED,
            'Provide a composer.json to be scanned',
            getcwd() . DIRECTORY_SEPARATOR . 'composer.json'
        );

        $this->addOption(
            'configuration',
            'c',
            InputOption::VALUE_OPTIONAL,
            'composer-unused configuration file',
            null
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $composerJsonPath = $input->getOption('composer-json');

        if (!file_exists($composerJsonPath) || !is_readable($composerJsonPath)) {
            $io->error(
                sprintf(
                    'composer.json on given path "%s" does not exist or is not readable.',
                    $composerJsonPath
                )
            );

            return Command::FAILURE;
        }

        $config = $this->configFactory->fromPath($composerJsonPath);
        $rootPackage = $this->packageFactory->fromConfig($config);
        $baseDir = dirname($composerJsonPath);
        $configuration = $this->configurationProvider->fromPath(
            $input->getOption('configuration') ?: $baseDir . DIRECTORY_SEPARATOR . 'composer-unused.php'
        );

        $symbols = $this->collectConsumedSymbolsCommandHandler->collect(
            new CollectConsumedSymbolsCommand(
                $baseDir,
                $rootPackage,
                [],
                $configuration
            )
        );

        $symbolNames = array_map(static function (SymbolInterface $symbol) {
            return $symbol->getIdentifier();
        }, iterator_to_array($symbols));

        $symbolNames = array_unique($symbolNames);
        sort($symbolNames);

        foreach ($symbolNames as $symbolName) {
            $output->writeln($symbolName);
        }

        return Command::SUCCESS;
    }
}
