<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Dependency\DependencyCollection;
use Icanhazstring\Composer\Unused\Dependency\DependencyInterface;
use Icanhazstring\Composer\Unused\Dependency\InvalidDependency;
use Icanhazstring\Composer\Unused\Dependency\RequiredDependency;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Icanhazstring\Composer\Unused\Subject\PackageSubject;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;
use Icanhazstring\Composer\Unused\UnusedPlugin;
use Icanhazstring\Composer\Unused\UseCase\CollectRequiredDependenciesUseCase;
use Icanhazstring\Composer\Unused\UseCase\CollectUsedSymbolsUseCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function array_merge;
use function dirname;
use function implode;
use function iterator_to_array;
use function sprintf;

class UnusedCommand extends BaseCommand
{
    /** @var ErrorHandlerInterface */
    private $errorHandler;
    /** @var SymfonyStyleFactory */
    private $symfonyStyleFactory;
    /** @var SymfonyStyle */
    private $io;
    /** @var LoggerInterface */
    private $logger;
    /** @var LoaderBuilder */
    private $loaderBuilder;
    /** @var CollectUsedSymbolsUseCase */
    private $collectUsedSymbolsUseCase;
    /** @var CollectRequiredDependenciesUseCase */
    private $collectRequiredDependenciesUseCase;

    public function __construct(
        ErrorHandlerInterface $errorHandler,
        SymfonyStyleFactory $outputFactory,
        LoaderBuilder $loaderBuilder,
        LoggerInterface $logger,
        CollectUsedSymbolsUseCase $collectUsedSymbolsUseCase,
        CollectRequiredDependenciesUseCase $collectRequiredDependenciesUseCase
    ) {
        parent::__construct('unused');
        $this->errorHandler = $errorHandler;
        $this->symfonyStyleFactory = $outputFactory;
        $this->loaderBuilder = $loaderBuilder;
        $this->logger = $logger;
        $this->collectUsedSymbolsUseCase = $collectUsedSymbolsUseCase;
        $this->collectRequiredDependenciesUseCase = $collectRequiredDependenciesUseCase;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Show unused packages by scanning and comparing package namespaces against your source.'
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
            'experimental',
            'x',
            InputOption::VALUE_NONE,
            'Run in experimental mode with new symbol scanning'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = ($this->symfonyStyleFactory)($input, $output);
        /** @var bool $isExperimentalMode */
        $isExperimentalMode = $input->getOption('experimental');
        /** @var Composer|null $composer */
        $composer = $this->getComposer();

        if ($composer === null) {
            $this->io->error('Could not get composer dependency');
            return 1;
        }

        $rootPackage = $composer->getPackage();

        if ($isExperimentalMode) {
            return $this->runExperimental($input, $output);
        }

        $this->logCommandInfo($composer);

        /** @var string[] $excludePackagesOption */
        $excludePackagesOption = $input->getOption('excludePackage');
        $excludePackagesConfig = $rootPackage->getExtra()['unused'] ?? [];

        /** @var string[] $excludeDirs */
        $excludeDirs = $input->getOption('excludeDir');

        /** @var bool $noProgress */
        $noProgress = $input->getOption('no-progress');

        $packageLoaderResult = $this->loaderBuilder
            ->build(PackageLoader::class, array_merge($excludePackagesConfig, $excludePackagesOption))
            ->toggleProgress($noProgress)
            ->load($composer, $this->io);

        if (!$packageLoaderResult->hasItems()) {
            $this->io->success('Done. No required packages to scan.');
            return 0;
        }

        $usageLoaderResult = $this->loaderBuilder
            ->build(UsageLoader::class, $excludeDirs)
            ->toggleProgress($noProgress)
            ->load($composer, $this->io);
        $analyseUsageResult = $this->analyseUsages($packageLoaderResult->getItems(), $usageLoaderResult->getItems());

        /** @var PackageSubject[] $usedPackages */
        $usedPackages = $analyseUsageResult['used'];
        /** @var PackageSubject[] $unusedPackages */
        $unusedPackages = $analyseUsageResult['unused'];

        $this->io->section('Results');

        $this->io->writeln(
            sprintf(
                'Found <fg=green>%d used</>, <fg=red>%d unused</> and <fg=yellow>%d ignored</> packages',
                count($usedPackages),
                count($unusedPackages),
                count($packageLoaderResult->getSkippedItems())
            )
        );

        $this->io->newLine();
        $this->io->text('<fg=green>Used packages</>');
        foreach ($usedPackages as $package) {
            $requiredBy = '';
            $suggestedBy = '';

            if (!empty($package->getRequiredBy())) {
                $requiredBy = sprintf(
                    ' (<fg=cyan>required by: %s</>)',
                    implode(', ', $package->getRequiredBy())
                );
            }

            if (!empty($package->getSuggestedBy())) {
                $suggestedBy = sprintf(
                    ' (<fg=cyan>suggested by: %s</>)',
                    implode(', ', $package->getSuggestedBy())
                );
            }

            $this->io->writeln(
                sprintf(
                    ' <fg=green>%s</> %s%s%s',
                    "\u{2713}",
                    $package->getName(),
                    $requiredBy,
                    $suggestedBy
                )
            );
        }

        $this->io->newLine();
        $this->io->text('<fg=red>Unused packages</>');
        foreach ($unusedPackages as $package) {
            $this->io->writeln(
                sprintf(
                    ' <fg=red>%s</> %s',
                    "\u{2717}",
                    $package->getName()
                )
            );
        }

        $this->io->newLine();
        $this->io->text('<fg=yellow>Ignored packages</>');

        foreach ($packageLoaderResult->getSkippedItems() as $skippedItem) {
            $this->io->writeln(
                sprintf(
                    ' <fg=yellow>%s</> %s (<fg=cyan>%s</>)',
                    "\u{25CB}",
                    $skippedItem['item'],
                    $skippedItem['reason']
                )
            );
        }

        if (count($unusedPackages) > 0 && !$input->getOption('ignore-exit-code')) {
            return 1;
        }

        return 0;
    }

    /**
     * @param PackageSubject[] $packages
     * @param UsageInterface[] $usages
     * @return array<string, array<PackageInterface|PackageSubject>>
     */
    private function analyseUsages(array $packages, array $usages): array
    {
        /** @var PackageInterface[] $unusedPackages */
        $unusedPackages = [];
        /** @var PackageInterface[] $usedPackages */
        $usedPackages = [];

        foreach ($packages as $package) {
            foreach ($usages as $usage) {
                try {
                    if (
                        $package->providesNamespace($usage->getNamespace())
                        || $package->getName() === $usage->getNamespace()
                    ) {
                        $usedPackages[] = $package;
                        continue 2;
                    }
                } catch (Throwable $throwable) {
                    $this->errorHandler->handle($throwable);
                }
            }

            foreach ($packages as $referencePackage) {
                $used = false;

                if ($referencePackage->suggestsPackage($package->getName())) {
                    $package->addSuggestedBy($referencePackage->getName());
                    $used = true;
                }

                if ($referencePackage->requiresPackage($package->getName())) {
                    $package->addRequiredBy($referencePackage->getName());
                    $used = true;
                }

                if ($used) {
                    $usedPackages[] = $package;
                    continue 2;
                }
            }

            $unusedPackages[] = $package;
        }

        return [
            'used' => $usedPackages,
            'unused' => $unusedPackages
        ];
    }

    private function logCommandInfo(Composer $composer): void
    {
        $requires = [];
        $devRequires = [];

        foreach ($composer->getPackage()->getRequires() as $name => $require) {
            $requires[$name] = $require->getPrettyConstraint();
        }

        foreach ($composer->getPackage()->getDevRequires() as $name => $require) {
            $devRequires[$name] = $require->getPrettyConstraint();
        }

        $this->logger->info('version', ['value' => UnusedPlugin::VERSION]);
        $this->logger->info('requires', $requires);
        $this->logger->info('dev-requires', $devRequires);
        $this->logger->info('autoload', $composer->getPackage()->getAutoload());
        $this->logger->info('dev-autoload', $composer->getPackage()->getDevAutoload());
    }

    private function runExperimental(InputInterface $input, OutputInterface $output): int
    {
        /** @var Composer|null $composer */
        $composer = $this->getComposer();

        if ($composer === null) {
            $this->io->error('Could not get composer dependency');
            return 1;
        }

        $composerBaseDir = dirname($composer->getConfig()->getConfigSource()->getName());
        $rootPackage = $composer->getPackage();

        $usedSymbols = iterator_to_array($this->collectUsedSymbolsUseCase->execute(
            $rootPackage,
            $composerBaseDir
        ));

        /** @var DependencyCollection<RequiredDependency> $requiredDependencyCollection */
        $requiredDependencyCollection = $this->collectRequiredDependenciesUseCase->execute(
            $rootPackage->getRequires(),
            $composer->getRepositoryManager()->getLocalRepository(),
            $composerBaseDir
        );

        foreach ($usedSymbols as $usedSymbol) {
            /** @var RequiredDependency $requiredDependency */
            foreach ($requiredDependencyCollection as $requiredDependency) {
                if ($requiredDependency->inState($requiredDependency::STATE_USED)) {
                    continue;
                }

                if ($requiredDependency->provides($usedSymbol)) {
                    $requiredDependency->markUsed();
                    continue;
                }

                /** @var RequiredDependency $secondRequiredDependency */
                foreach ($requiredDependencyCollection as $secondRequiredDependency) {
                    if ($requiredDependency === $secondRequiredDependency) {
                        continue;
                    }

                    if ($secondRequiredDependency->requires($requiredDependency)) {
                        // TODO add "required by" in output
                        $requiredDependency->markUsed();
                    }
                }
            }
        }

        [$usedDependencyCollection, $unusedDependencyCollection] = $requiredDependencyCollection->partition(
            static function (DependencyInterface $dependency) {
                return $dependency->inState($dependency::STATE_USED);
            }
        );

        /** @var DependencyCollection<InvalidDependency> $invalidDependencyCollection */
        [$invalidDependencyCollection, $unusedDependencyCollection] = $unusedDependencyCollection->partition(
            static function (DependencyInterface $dependency) {
                return $dependency->inState($dependency::STATE_INVALID);
            }
        );

        $this->io->section('Results');

        $this->io->writeln(
            sprintf(
                'Found <fg=green>%d used</>, <fg=red>%d unused</> and <fg=yellow>%d ignored</> packages',
                count($usedDependencyCollection),
                count($unusedDependencyCollection),
                count($invalidDependencyCollection)
            )
        );

        $this->io->newLine();
        $this->io->text('<fg=green>Used packages</>');
        foreach ($usedDependencyCollection as $usedDependency) {
            // TODO add required by dependency
            // TODO add suggest by dependency

            $this->io->writeln(
                sprintf(
                    ' <fg=green>%s</> %s',
                    "\u{2713}",
                    $usedDependency->getName()
                )
            );
        }

        $this->io->newLine();
        $this->io->text('<fg=red>Unused packages</>');
        foreach ($unusedDependencyCollection as $dependency) {
            $this->io->writeln(
                sprintf(
                    ' <fg=red>%s</> %s',
                    "\u{2717}",
                    $dependency->getName()
                )
            );
        }

        $this->io->newLine();
        $this->io->text('<fg=yellow>Ignored packages</>');

        foreach ($invalidDependencyCollection as $dependency) {
            $this->io->writeln(
                sprintf(
                    ' <fg=yellow>%s</> %s (<fg=cyan>%s</>)',
                    "\u{25CB}",
                    $dependency->getName(),
                    $dependency->getReason()
                )
            );
        }

        return 0;
    }
}
