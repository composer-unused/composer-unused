<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Console\Command;

use ComposerUnused\ComposerUnused\Composer\ConfigFactory;
use ComposerUnused\ComposerUnused\Composer\Link;
use ComposerUnused\ComposerUnused\Composer\LocalPackageInstalledPath;
use ComposerUnused\ComposerUnused\Composer\LocalRepositoryFactory;
use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\ConfigurationProvider;
use ComposerUnused\ComposerUnused\PackageResolver;
use ComposerUnused\ComposerUnused\Symbol\ProvidedSymbolLoaderBuilder;
use ComposerUnused\SymbolParser\Symbol\SymbolInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'debug:provided-symbols')]
final class DebugProvidedSymbolsCommand extends Command
{
    private ConfigFactory $configFactory;
    private PackageResolver $packageResolver;
    private ProvidedSymbolLoaderBuilder $providedSymbolLoaderBuilder;
    private LocalRepositoryFactory $localRepositoryFactory;
    private ConfigurationProvider $configurationProvider;

    public function __construct(
        ConfigFactory $configFactory,
        PackageResolver $packageResolver,
        ProvidedSymbolLoaderBuilder $providedSymbolLoaderBuilder,
        LocalRepositoryFactory $localRepositoryFactory,
        ConfigurationProvider $configurationProvider
    ) {
        parent::__construct();
        $this->configFactory = $configFactory;
        $this->packageResolver = $packageResolver;
        $this->providedSymbolLoaderBuilder = $providedSymbolLoaderBuilder;
        $this->localRepositoryFactory = $localRepositoryFactory;
        $this->configurationProvider = $configurationProvider;
    }

    protected function configure(): void
    {
        $this->setDescription('List all provided symbols from the given package.');
        $this->addArgument('package', InputArgument::REQUIRED, 'Compose package to list defined symbols');

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
                    'composer.json on given path %s does not exist or is not readable.',
                    $composerJsonPath
                )
            );

            return Command::FAILURE;
        }

        $config = $this->configFactory->fromPath($composerJsonPath);
        $baseDir = dirname($composerJsonPath);
        $localRepository = $this->localRepositoryFactory->create(
            new LocalPackageInstalledPath($config)
        );
        $package = $input->getArgument('package');
        $configuration = $this->configurationProvider->fromPath(
            $input->getOption('configuration') ?: $baseDir . DIRECTORY_SEPARATOR . 'composer-unused.php'
        );

        $composerPackage = $this->packageResolver->resolve(
            new Link($package, 0),
            $localRepository
        );

        if ($composerPackage === null) {
            $io->error(
                sprintf('Package "%s" is not installed.', $package)
            );

            return Command::FAILURE;
        }

        $packageBaseDir = $baseDir . DIRECTORY_SEPARATOR . $composerPackage->getName();

        $providedSymbolLoader = $this
            ->providedSymbolLoaderBuilder
            ->setAdditionalFiles(($configuration)->getAdditionalFilesFor($composerPackage->getName()))
            ->build();

        $symbols = $providedSymbolLoader->withBaseDir($packageBaseDir)->load($composerPackage);

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
