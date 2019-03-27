<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command;

use Composer\Command\BaseCommand;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Error\ErrorDumperInterface;
use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Log\DebugLogger;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class UnusedCommand extends BaseCommand
{
    /** @var ErrorHandlerInterface */
    private $errorHandler;
    /** @var ErrorDumperInterface */
    private $errorDumper;
    /** @var SymfonyStyleFactory */
    private $symfonyStyleFactory;
    /** @var SymfonyStyle */
    private $io;
    /** @var DebugLogger */
    private $debugLogger;
    /** @var IOInterface */
    private $composerIO;
    /** @var LoaderBuilder */
    private $loaderBuilder;

    public function __construct(
        ErrorHandlerInterface $errorHandler,
        ErrorDumperInterface $errorDumper,
        SymfonyStyleFactory $outputFactory,
        LoaderBuilder $loaderBuilder,
        DebugLogger $debugLogger,
        IOInterface $composerIO
    ) {
        parent::__construct('unused');
        $this->errorHandler = $errorHandler;
        $this->errorDumper = $errorDumper;
        $this->symfonyStyleFactory = $outputFactory;
        $this->loaderBuilder = $loaderBuilder;
        $this->debugLogger = $debugLogger;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $excludePackages */
        $excludePackages = $input->getOption('excludePackage');
        /** @var string[] $excludeDirs */
        $excludeDirs = $input->getOption('excludeDir');

        $packageLoader = $this->loaderBuilder->build(PackageLoader::class, $excludePackages);
        $usageLoader = $this->loaderBuilder->build(UsageLoader::class, $excludeDirs);

        $this->io = ($this->symfonyStyleFactory)($input, $output);

        $composer = $this->getComposer();

        $packageLoaderResult = $packageLoader->load($composer, $this->io);

        if (!$packageLoaderResult->hasItems()) {
            $this->io->error('No required packages found');
            $this->dumpLogs();

            return 1;
        }

        $usageLoaderResult = $usageLoader->load($composer, $this->io);

        if (!$usageLoaderResult->hasItems()) {
            $this->io->error('No usages could be found. Aborting.');
            $this->dumpLogs();

            return 1;
        }

        $analyseUsageResult = $this->analyseUsages($packageLoaderResult->getItems(), $usageLoaderResult->getItems());

        /** @var PackageInterface[] $usedPackages */
        $usedPackages = $analyseUsageResult['used'];
        /** @var PackageInterface[] $unusedPackages */
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
            $this->io->writeln(
                sprintf(
                    ' <fg=green>%s</> %s',
                    "\u{2713}",
                    $package->getName()
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

        return 0;
    }

    private function dumpLogs(): void
    {
        if (!$this->composerIO->isDebug()) {
            return;
        }

        $dumpLocation = $this->errorDumper->dump($this->errorHandler, $this->debugLogger);
        if ($dumpLocation) {
            $this->io->note(sprintf('Log dumped to: %s', $dumpLocation));
        }
    }

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

            $unusedPackages[] = $package;
        }

        return [
            'used'   => $usedPackages,
            'unused' => $unusedPackages
        ];
    }
}
