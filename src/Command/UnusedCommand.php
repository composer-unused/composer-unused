<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Error\ErrorDumperInterface;
use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use Icanhazstring\Composer\Unused\Parser\Strategy\NewParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\StaticParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\UseParseStrategy;
use Icanhazstring\Composer\Unused\Subject\PackageSubject;
use Icanhazstring\Composer\Unused\Subject\SubjectInterface;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UnusedCommand extends BaseCommand
{
    /** @var ErrorHandlerInterface */
    private $errorHandler;
    /** @var ErrorDumperInterface */
    private $errorDumper;
    /** @var SymfonyStyleFactory */
    private $symfonyStyleFactory;

    public function __construct(
        ErrorHandlerInterface $errorHandler,
        ErrorDumperInterface $errorDumper,
        SymfonyStyleFactory $outputFactory
    ) {
        parent::__construct('unused');
        $this->errorHandler = $errorHandler;
        $this->errorDumper = $errorDumper;
        $this->symfonyStyleFactory = $outputFactory;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Show unused packages by scanning by comparing package namespaces against your source.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var SymfonyStyle $io */
        $io = ($this->symfonyStyleFactory)($input, $output);

        $composer = $this->getComposer();
        $packages = $this->loadPackages($composer, $io);

        if (empty($packages)) {
            $io->error('No required packages found');

            return 1;
        }

        $io->note(sprintf('Found %d package(s) to be checked.', count($packages)));

        $usages = $this->loadUsages($composer, $io);

        if (empty($usages)) {
            $io->error('No usages could be found. Aborting.');

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

        $io->writeln(
            sprintf(
                'Found <fg=green>%d usued</> and <fg=red>%d unused</> packages',
                count($usedPackages),
                count($unusedPackages)
            )
        );

        if ($this->errorHandler->hasErrors()) {
            $io->warning('Errors occured during scanning process');

            $dumpLocation = $this->errorDumper->dump($this->errorHandler);
            if ($dumpLocation) {
                $io->note(sprintf('ErrorLog dumped to: %s', $dumpLocation));
            }
        }

        $io->section('Results');

        $io->text('<fg=green>Used packages</>');
        foreach ($usedPackages as $package) {
            $io->writeln(sprintf(' * %s <fg=green>%s</>', $package->getName(), "\u{2713}"));
        }

        $io->newLine();
        $io->text('<fg=red>Unused packages</>');
        foreach ($unusedPackages as $package) {
            $io->writeln(sprintf(' * %s <fg=red>%s</>', $package->getName(), "\u{2717}"));
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
        $io->section('Loading packages');

        $requiredPackages = $composer->getPackage()->getRequires();
        $localRepo = $composer->getRepositoryManager()->getLocalRepository();

        $packages = [];
        /** @var string[] $skipped */
        $skipped = [];

        if (empty($requiredPackages)) {
            return [];
        }

        $io->text(sprintf('Loading %d requirements', count($requiredPackages)));
        $io->progressStart(\count($requiredPackages));

        foreach ($requiredPackages as $index => $require) {
            $constraint = $require->getConstraint();

            if ($constraint === null) {
                $io->progressAdvance();
                $skipped[] = $require->getTarget();
                continue;
            }

            $composerPackage = $localRepo->findPackage($require->getTarget(), $constraint);

            if ($composerPackage === null) {
                $io->progressAdvance();
                $skipped[] = $require->getTarget();
                continue;
            }

            $packages[] = new PackageSubject($composerPackage);
            $io->progressAdvance();
        }

        $io->progressFinish();

        if (count($skipped)) {
            $io->note(sprintf('Skipped %d requirements. No package found or invalid constraint.', count($skipped)));
            $io->listing($skipped);
        }

        return $packages;
    }

    /**
     * @param Composer     $composer
     * @param SymfonyStyle $io
     * @return UsageInterface[]
     */
    private function loadUsages(Composer $composer, SymfonyStyle $io): array
    {
        $autoload = array_merge_recursive(
            $composer->getPackage()->getAutoload(),
            $composer->getPackage()->getDevAutoload()
        );

        $autoloadDirs = [];
        $autoloadFiles = [];

        foreach ($autoload as $autoloadType => $namespaces) {
            foreach ($namespaces as $namespace => $paths) {
                if (!is_array($paths)) {
                    $paths = [$paths];
                }

                foreach ($paths as $path) {
                    $resolvePath = stream_resolve_include_path($path);

                    if (!$resolvePath) {
                        continue;
                    }

                    if (in_array($autoloadType, ['classmap', 'files']) && is_file($path)) {
                        $autoloadFiles[] = new SplFileInfo($path, pathinfo($path, PATHINFO_DIRNAME), $path);
                        continue;
                    }

                    $autoloadDirs[] = $resolvePath;
                }
            }
        }

        if (empty($autoloadDirs) && empty($autoloadFiles)) {
            $io->error('Could not load paths from root package to scan.');

            return [];
        }

        $finder = new Finder();
        /** @var SplFileInfo[] $files */
        $files = $finder->files()->name('*.php')->in($autoloadDirs)->append($autoloadFiles);

        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $visitor = new NodeVisitor([
            new NewParseStrategy(),
            new StaticParseStrategy(),
            new UseParseStrategy()
        ], $this->errorHandler);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);

        $io->section('Scanning files...');
        $io->progressStart(count($files));

        foreach ($files as $file) {
            $visitor->setCurrentFile($file);
            $nodes = $parser->parse($file->getContents(), $this->errorHandler) ?? [];

            if (!$nodes) {
                $io->progressAdvance();
                continue;
            }

            $traverser->traverse($nodes);
            $io->progressAdvance();
        }

        $io->progressFinish();

        return $visitor->getUsages();
    }
}
