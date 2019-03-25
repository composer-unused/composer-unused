<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Error\ErrorDumperInterface;
use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\LoaderInterface;
use Icanhazstring\Composer\Unused\Log\DebugLogger;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Icanhazstring\Composer\Unused\Subject\SubjectInterface;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
    /** @var LoaderInterface */
    private $usageLoader;
    /** @var LoaderInterface */
    private $packageLoader;
    /** @var DebugLogger */
    private $debugLogger;
    /** @var IOInterface */
    private $composerIO;

    public function __construct(
        ErrorHandlerInterface $errorHandler,
        ErrorDumperInterface $errorDumper,
        SymfonyStyleFactory $outputFactory,
        LoaderInterface $usageLoader,
        LoaderInterface $packageLoader,
        DebugLogger $debugLogger,
        IOInterface $composerIO
    ) {
        parent::__construct('unused');
        $this->errorHandler = $errorHandler;
        $this->errorDumper = $errorDumper;
        $this->symfonyStyleFactory = $outputFactory;
        $this->usageLoader = $usageLoader;
        $this->packageLoader = $packageLoader;
        $this->debugLogger = $debugLogger;
        $this->composerIO = $composerIO;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Show unused packages by scanning by comparing package namespaces against your source.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = ($this->symfonyStyleFactory)($input, $output);

        $composer = $this->getComposer();
        $packages = $this->loadPackages($composer, $this->io);

        if (empty($packages)) {
            $this->io->error('No required packages found');
            $this->dumpLogs();

            return 1;
        }

        $this->io->note(sprintf('Found %d package(s) to be checked.', count($packages)));

        $usages = $this->loadUsages($composer, $this->io);

        if (empty($usages)) {
            $this->io->error('No usages could be found. Aborting.');
            $this->dumpLogs();

            return 1;
        }

        /** @var PackageInterface[] $unusedPackages */
        $unusedPackages = [];
        /** @var PackageInterface[] $usedPackages */
        $usedPackages = [];

        foreach ($packages as $package) {
            foreach ($usages as $usage) {
                if ($package->providesNamespace($usage->getNamespace())) {
                    $usedPackages[] = $package;
                    continue 2;
                }
            }

            $unusedPackages[] = $package;
        }

        $this->io->writeln(
            sprintf(
                'Found <fg=green>%d used</> and <fg=red>%d unused</> packages',
                count($usedPackages),
                count($unusedPackages)
            )
        );

        $this->io->section('Results');

        $this->io->text('<fg=green>Used packages</>');
        foreach ($usedPackages as $package) {
            $this->io->writeln(sprintf(' * %s <fg=green>%s</>', $package->getName(), "\u{2713}"));
        }

        $this->io->newLine();
        $this->io->text('<fg=red>Unused packages</>');
        foreach ($unusedPackages as $package) {
            $this->io->writeln(sprintf(' * %s <fg=red>%s</>', $package->getName(), "\u{2717}"));
        }

        if ($this->composerIO->isDebug()) {
            $this->dumpLogs();
        }

        return 0;
    }

    /**
     * @param Composer     $composer
     * @param SymfonyStyle $io
     * @return SubjectInterface[]
     */
    private function loadPackages(Composer $composer, SymfonyStyle $io): array
    {
        return $this->packageLoader->load($composer, $io);
    }

    /**
     * @param Composer     $composer
     * @param SymfonyStyle $io
     * @return UsageInterface[]
     */
    private function loadUsages(Composer $composer, SymfonyStyle $io): array
    {
        return $this->usageLoader->load($composer, $io);
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
}
