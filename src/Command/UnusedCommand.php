<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command;

use Composer\Command\BaseCommand;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Icanhazstring\Composer\Unused\Subject\PackageSubject;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;
use Icanhazstring\Composer\Unused\UnusedPlugin;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

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
    /** @var IOInterface */
    private $composerIO;
    /** @var LoaderBuilder */
    private $loaderBuilder;

    public function __construct(
        ErrorHandlerInterface $errorHandler,
        SymfonyStyleFactory $outputFactory,
        LoaderBuilder $loaderBuilder,
        LoggerInterface $logger,
        IOInterface $composerIO
    ) {
        parent::__construct('unused');
        $this->errorHandler = $errorHandler;
        $this->symfonyStyleFactory = $outputFactory;
        $this->loaderBuilder = $loaderBuilder;
        $this->logger = $logger;
        $this->composerIO = $composerIO;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Show unused packages by scanning and comparing package namespaces against your source.'
        );

        $this->addOption(
            'excludeDir',
            'xd',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Provide one or more folders to exclude from usage scan',
            []
        );

        $this->addOption(
            'excludePackage',
            'xp',
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composer = $this->getComposer();
        $this->logCommandInfo($composer);

        /** @var string[] $excludePackagesOption */
        $excludePackagesOption = $input->getOption('excludePackage');
        $excludePackagesConfig = $composer->getPackage()->getExtra()['unused'] ?? [];

        /** @var string[] $excludeDirs */
        $excludeDirs = $input->getOption('excludeDir');

        $this->io = ($this->symfonyStyleFactory)($input, $output);

        $packageLoaderResult = $this->loaderBuilder
            ->build(PackageLoader::class, array_merge($excludePackagesConfig, $excludePackagesOption))
            ->load($composer, $this->io);

        if (!$packageLoaderResult->hasItems()) {
            $this->io->error('No required packages found');
            $this->dumpLogs();

            return 1;
        }

        $usageLoaderResult = $this->loaderBuilder
            ->build(UsageLoader::class, $excludeDirs)
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

        if ($this->composerIO->isDebug()) {
            $this->dumpLogs();
        }

        if ($packageLoaderResult->hasSkippedItems() && !$input->getOption('ignore-exit-code')) {
            return 1;
        }

        return 0;
    }

    private function dumpLogs(): void
    {
        if (!$this->composerIO->isDebug()) {
            return;
        }

//        $dumpLocation = $this->errorDumper->dump($this->errorHandler, $this->logger);
//        if ($dumpLocation) {
//            $this->io->note(sprintf('Log dumped to: %s', $dumpLocation));
//        }
    }

    /**
     * @param PackageSubject[] $packages
     * @param UsageInterface[] $usages
     * @return array
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
                    if ($package->providesNamespace($usage->getNamespace())) {
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
            'used'   => $usedPackages,
            'unused' => $unusedPackages
        ];
    }

    private function logCommandInfo(\Composer\Composer $composer): void
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
}
