<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Console\Command;

use ComposerUnused\ComposerUnused\Command\CollectConsumedSymbolsCommand;
use ComposerUnused\ComposerUnused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use ComposerUnused\ComposerUnused\Command\Handler\CollectRequiredDependenciesCommandHandler;
use ComposerUnused\ComposerUnused\Command\LoadRequiredDependenciesCommand;
use ComposerUnused\ComposerUnused\Composer\ConfigFactory;
use ComposerUnused\ComposerUnused\Composer\Exception\InvalidComposerVersionInstalledPackages;
use ComposerUnused\ComposerUnused\Composer\LocalPackageInstalledPath;
use ComposerUnused\ComposerUnused\Composer\LocalRepositoryFactory;
use ComposerUnused\ComposerUnused\Composer\PackageFactory;
use ComposerUnused\ComposerUnused\Configuration\ConfigurationProvider;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;
use ComposerUnused\ComposerUnused\Console\Progress\DefaultProgressBarDecorator;
use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;
use ComposerUnused\ComposerUnused\Dependency\RequiredDependency;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\ComposerUnused\OutputFormatter\FormatterFactory;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function dirname;
use function file_put_contents;
use function is_array;
use function is_writable;
use function sprintf;

use const DIRECTORY_SEPARATOR;

#[AsCommand(name: 'unused')]
final class UnusedCommand extends Command
{
    private CollectConsumedSymbolsCommandHandler $collectConsumedSymbolsCommandHandler;
    private CollectRequiredDependenciesCommandHandler $collectRequiredDependenciesCommandHandler;
    private ConfigFactory $configFactory;
    private FormatterFactory $formatterFactory;
    private LocalRepositoryFactory $localRepositoryFactory;
    private PackageFactory $packageFactory;
    private ConfigurationProvider $configurationProvider;

    public function __construct(
        ConfigFactory $configFactory,
        CollectConsumedSymbolsCommandHandler $collectConsumedSymbolsCommandHandler,
        CollectRequiredDependenciesCommandHandler $collectRequiredDependenciesCommandHandler,
        FormatterFactory $formatterFactory,
        LocalRepositoryFactory $localRepositoryFactory,
        PackageFactory $packageFactory,
        ConfigurationProvider $configurationProvider
    ) {
        parent::__construct();
        $this->configFactory = $configFactory;
        $this->collectConsumedSymbolsCommandHandler = $collectConsumedSymbolsCommandHandler;
        $this->collectRequiredDependenciesCommandHandler = $collectRequiredDependenciesCommandHandler;
        $this->formatterFactory = $formatterFactory;
        $this->localRepositoryFactory = $localRepositoryFactory;
        $this->packageFactory = $packageFactory;
        $this->configurationProvider = $configurationProvider;
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
            'Change output style (default, compact, github, json, junit, gitlab)'
        );

        $this->addOption(
            'output-file',
            null,
            InputOption::VALUE_REQUIRED,
            'Write output to given file'
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
            InputOption::VALUE_OPTIONAL,
            'composer-unused configuration file',
            null
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $outputFormatter = $this->formatterFactory->create($input->getOption('output-format'));
        $composerJsonPath = $input->getArgument('composer-json');
        $ignoreExitCode = (bool)$input->getOption('ignore-exit-code');
        /** @var string|list<string> $excludedDirs */
        $excludedDirs = $input->getOption('excludeDir');
        $outputFile = $input->getOption('output-file');

        if (!is_array($excludedDirs)) {
            $excludedDirs = [$excludedDirs];
        }

        if (!file_exists($composerJsonPath) || !is_readable($composerJsonPath)) {
            $io->error(
                sprintf(
                    'composer.json on given path %s does not exist or is not readable.',
                    $composerJsonPath
                )
            );

            return $ignoreExitCode ? 0 : 1;
        }

        if ($outputFile) {
            if (!is_writable(dirname($outputFile))) {
                $io->error(
                    sprintf(
                        'The directory of the output file %s is not writable.',
                        $outputFile
                    )
                );

                return $ignoreExitCode ? 0 : 1;
            }

            $bufferedOutput = new BufferedOutput();
            $formatOutput = new SymfonyStyle($input, $bufferedOutput);
        } else {
            $formatOutput = $io;
        }

        try {
            $composerConfig = $this->configFactory->fromPath($composerJsonPath);
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return $ignoreExitCode ? 0 : 1;
        }

        $rootPackage = $this->packageFactory->fromConfig($composerConfig);

        try {
            $localRepository = $this->localRepositoryFactory->create(
                new LocalPackageInstalledPath($composerConfig)
            );
        } catch (InvalidComposerVersionInstalledPackages $exception) {
            $io->warning("Composer Version 1 is not supported");
            return $ignoreExitCode ? 0 : 1;
        }

        $baseDir = $composerConfig->getBaseDir();
        $configuration = $this->configurationProvider->fromPath(
            $input->getOption('configuration') ?: $baseDir . DIRECTORY_SEPARATOR . 'composer-unused.php'
        );

        $consumedSymbols = $this->collectConsumedSymbolsCommandHandler->collect(
            new CollectConsumedSymbolsCommand(
                $baseDir,
                $rootPackage,
                $excludedDirs,
                $configuration
            )
        );

        $requiredDependencyCollection = $this->collectRequiredDependenciesCommandHandler->collect(
            new LoadRequiredDependenciesCommand(
                $configuration->getDependenciesDir() ?? $baseDir . DIRECTORY_SEPARATOR . $composerConfig->get('vendor-dir'),
                $rootPackage->getRequires(),
                $localRepository,
                $configuration,
                new DefaultProgressBarDecorator(
                    $io,
                    count($rootPackage->getRequires()),
                    $input->getOption('no-progress')
                )
            )
        );

        if (isset($composerConfig->getExtra()['unused'])) {
            $io->warning(
                'composer.json[extra][unused] is deprecated and will be removed. ' .
                'Consider migrating to composer-unused.php configuration.'
            );
        }
        if (array_key_exists('', $rootPackage->getAutoload()['psr-4'] ?? [])) {
            $io->warning([
                'composer.json[autoload][psr-4] contains an empty namespace.',
                'It\'s usually a bad idea for performance, see output of "composer validate" command.'
            ]);
        }

        $filterCollection = new FilterCollection(
            array_merge(
                array_values($configuration->getNamedFilters()),
                array_map(
                    static fn(string $filter) => NamedFilter::fromString($filter),
                    array_merge(
                        $input->getOption('excludePackage'),
                        $composerConfig->getExtra()['unused'] ?? []
                    )
                )
            ),
            array_values($configuration->getPatternFilters())
        );

        foreach ($consumedSymbols as $symbol) {
            /** @var RequiredDependency $requiredDependency */
            foreach ($requiredDependencyCollection as $requiredDependency) {
                if ($requiredDependency->inState($requiredDependency::STATE_USED)) {
                    continue;
                }

                if ($requiredDependency->getName() === 'php' || $requiredDependency->provides($symbol)) {
                    $requiredDependency->markUsed();
                }
            }
        }

        foreach ($requiredDependencyCollection as $requiredDependency) {
            if ($requiredDependency->inState($requiredDependency::STATE_USED)) {
                continue;
            }

            /** @var RequiredDependency $secondRequiredDependency */
            foreach ($requiredDependencyCollection as $secondRequiredDependency) {
                if ($requiredDependency === $secondRequiredDependency) {
                    continue;
                }

                $secondaryIsUsed = $secondRequiredDependency->inState($requiredDependency::STATE_USED);

                if ($secondaryIsUsed && $secondRequiredDependency->requires($requiredDependency)) {
                    $requiredDependency->requiredBy($secondRequiredDependency);
                    $requiredDependency->markUsed();
                    continue 2;
                }

                if ($secondaryIsUsed && $secondRequiredDependency->suggests($requiredDependency)) {
                    $requiredDependency->suggestedBy($secondRequiredDependency);
                    $requiredDependency->markUsed();
                    continue 2;
                }
            }
        }

        [$usedDependencyCollection, $unusedDependencyCollection] = $requiredDependencyCollection->partition(
            static function (DependencyInterface $dependency) {
                return $dependency->inState($dependency::STATE_USED);
            }
        );

        $unusedDependencyCollection->map(
            static function (DependencyInterface $dependency) use ($filterCollection) {
                foreach ($filterCollection as $filter) {
                    if ($filter->applies($dependency)) {
                        $dependency->markIgnored('ignored by ' . $filter->toString());
                    }
                }
            }
        );

        [$ignoredDependencyCollection, $unusedDependencyCollection] = $unusedDependencyCollection->partition(
            static fn(
                DependencyInterface $dependency
            ) => $dependency->inState($dependency::STATE_IGNORED) || $dependency->inState($dependency::STATE_INVALID)
        );

        $exitCode = $outputFormatter->formatOutput(
            $rootPackage,
            $composerConfig->getFileName(),
            $usedDependencyCollection,
            $unusedDependencyCollection,
            $ignoredDependencyCollection,
            $filterCollection,
            $formatOutput
        );

        if ($outputFile) {
            file_put_contents($outputFile, $bufferedOutput->fetch());
        }

        return !$ignoreExitCode ? $exitCode : 0;
    }
}
