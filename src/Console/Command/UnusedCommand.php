<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Console\Command;

use ComposerUnused\ComposerUnused\Command\CollectConsumedSymbolsCommand;
use ComposerUnused\ComposerUnused\Command\FilterDependencyCollectionCommand;
use ComposerUnused\ComposerUnused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use ComposerUnused\ComposerUnused\Command\Handler\CollectFilteredDependenciesCommandHandler;
use ComposerUnused\ComposerUnused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use ComposerUnused\ComposerUnused\Command\LoadRequiredDependenciesCommand;
use ComposerUnused\ComposerUnused\Composer\ConfigFactory;
use ComposerUnused\ComposerUnused\Composer\LocalRepository;
use ComposerUnused\ComposerUnused\Composer\PackageFactory;
use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\ComposerUnused\OutputFormatter\FormatterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function sprintf;

use const DIRECTORY_SEPARATOR;

final class UnusedCommand extends Command
{
    public const VERSION = '0.8.1';

    private CollectConsumedSymbolsCommandHandler $collectConsumedSymbolsCommandHandler;
    private CollectRequiredDependenciesCommandHandler $collectRequiredDependenciesCommandHandler;
    private CollectFilteredDependenciesCommandHandler $collectFilteredDependenciesCommandHandler;
    private ConfigFactory $configFactory;

    public function __construct(
        ConfigFactory $configFactory,
        CollectConsumedSymbolsCommandHandler $collectConsumedSymbolsCommandHandler,
        CollectRequiredDependenciesCommandHandler $collectRequiredDependenciesCommandHandler,
        CollectFilteredDependenciesCommandHandler $collectFilteredDependenciesCommandHandler
    ) {
        parent::__construct('unused');
        $this->configFactory = $configFactory;
        $this->collectConsumedSymbolsCommandHandler = $collectConsumedSymbolsCommandHandler;
        $this->collectRequiredDependenciesCommandHandler = $collectRequiredDependenciesCommandHandler;
        $this->collectFilteredDependenciesCommandHandler = $collectFilteredDependenciesCommandHandler;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Show unused packages by scanning and comparing package namespaces against your source.'
        );

        $this->addArgument(
            'composer-json',
            InputArgument::OPTIONAL,
            'Provide a composer.json to be scanned',
            getcwd() . DIRECTORY_SEPARATOR . 'composer.json'
        );

        $this->addOption(
            'output-format',
            'o',
            InputOption::VALUE_REQUIRED,
            'Change output style (default, github)'
        );

        $this->addOption(
            'excludeDir',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Provide one or more folders to exclude from usage scan',
            []
        );

        $this->addOption(
            'excludePackage',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Provide one or more packages that should be ignored during scan',
            []
        );

        $this->addOption(
            'ignore-exit-code',
            null,
            InputOption::VALUE_NONE,
            'Ignore exit codes so there are no "failure" exit codes'
        );

        $this->addOption(
            'no-progress',
            null,
            InputOption::VALUE_NONE,
            'Show no progress bar'
        );

        $this->addOption(
            'configuration',
            'c',
            InputOption::VALUE_REQUIRED,
            'composer-unused configuration file',
            getcwd() . DIRECTORY_SEPARATOR . 'composer-unused.json.dist'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $outputFormatter = FormatterFactory::create($input->getOption('output-format'));
        $composerJsonPath = $input->getArgument('composer-json');

        if (!file_exists($composerJsonPath) && !is_readable($composerJsonPath)) {
            $io->error(
                sprintf(
                    'composer.json on given path %s does not exist or is not readable.',
                    $composerJsonPath
                )
            );

            return 1;
        }

        if (!file_exists($composerJsonPath)) {
            $io->error('composer.json not present in: ' . dirname($composerJsonPath));
            return 1;
        }

        $composerJson = file_get_contents($composerJsonPath);

        if ($composerJson === false) {
            $io->error('Unable to read contents from given composer.json');
            return 1;
        }

        $composerConfig = $this->configFactory->fromComposerJson($composerJson);
        $baseDir = dirname($composerJsonPath);

        $rootPackage = PackageFactory::fromConfig($composerConfig, $composerJson);
        $localRepository = new LocalRepository($baseDir . DIRECTORY_SEPARATOR . $composerConfig->get('vendor-dir'));

        $consumedSymbols = $this->collectConsumedSymbolsCommandHandler->collect(
            new CollectConsumedSymbolsCommand(
                $baseDir,
                $rootPackage
            )
        );

        $unfilteredRequiredDependencyCollection = $this->collectRequiredDependenciesCommandHandler->collect(
            new LoadRequiredDependenciesCommand(
                $baseDir . DIRECTORY_SEPARATOR . $composerConfig->get('vendor-dir'),
                $rootPackage->getRequires(),
                $localRepository
            )
        );

        $filterCollection = new FilterCollection(
            array_merge(
                $composerConfig->getExtra()['unused'] ?? [],
                $input->getOption('excludePackage')
            ),
            [] // TODO pattern exclusion from CLI
        );

        $requiredDependencyCollection = $this->collectFilteredDependenciesCommandHandler->collect(
            new FilterDependencyCollectionCommand(
                $unfilteredRequiredDependencyCollection,
                $filterCollection
            )
        );

        foreach ($consumedSymbols as $symbol) {
            /** @var RequiredDependency $requiredDependency */
            foreach ($requiredDependencyCollection as $requiredDependency) {
                if ($requiredDependency->inState($requiredDependency::STATE_USED)) {
                    continue;
                }

                if ($requiredDependency->getName() === 'php' || $requiredDependency->provides($symbol)) {
                    $requiredDependency->markUsed();
                    continue;
                }

                /** @var RequiredDependency $secondRequiredDependency */
                foreach ($requiredDependencyCollection as $secondRequiredDependency) {
                    if ($requiredDependency === $secondRequiredDependency) {
                        continue;
                    }

                    if ($secondRequiredDependency->requires($requiredDependency)) {
                        $requiredDependency->requiredBy($secondRequiredDependency);
                        $requiredDependency->markUsed();
                        continue 2;
                    }

                    if ($secondRequiredDependency->suggests($requiredDependency)) {
                        $requiredDependency->suggestedBy($secondRequiredDependency);
                        $requiredDependency->markUsed();
                        continue 2;
                    }
                }
            }
        }

        [$usedDependencyCollection, $unusedDependencyCollection] = $requiredDependencyCollection->partition(
            static function (DependencyInterface $dependency) {
                return $dependency->inState($dependency::STATE_USED);
            }
        );

        [$invalidDependencyCollection, $unusedDependencyCollection] = $unusedDependencyCollection->partition(
            static function (DependencyInterface $dependency) {
                return $dependency->inState($dependency::STATE_INVALID);
            }
        );

        return $outputFormatter->formatOutput(
            $rootPackage,
            $composerJsonPath,
            $usedDependencyCollection,
            $unusedDependencyCollection,
            $invalidDependencyCollection,
            $filterCollection,
            $io
        );
    }
}
