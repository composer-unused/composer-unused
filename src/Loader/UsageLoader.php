<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use Icanhazstring\Composer\Unused\Subject\PackageSubject;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UsageLoader implements LoaderInterface
{
    /** @var Parser */
    private $parser;
    /** @var NodeVisitor */
    private $visitor;
    /** @var ErrorHandlerInterface */
    private $errorHandler;

    public function __construct(
        Parser $parser,
        NodeVisitor $visitor,
        ErrorHandlerInterface $errorHandler
    ) {
        $this->parser = $parser;
        $this->visitor = $visitor;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @param Composer     $composer
     * @param SymfonyStyle $io
     *
     * @return PackageSubject[]
     */
    public function load(Composer $composer, SymfonyStyle $io): array
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
                    $io->writeln(sprintf('Trying to resolve path %s', $path), OutputInterface::VERBOSITY_DEBUG);

                    $resolvePath = stream_resolve_include_path($path);

                    if (!$resolvePath) {
                        $io->writeln(
                            sprintf('Skipped: Could not resolve %s', $path),
                            OutputInterface::VERBOSITY_DEBUG
                        );

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

        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->visitor);

        $io->section('Scanning files...');

        $io->progressStart(count($files));

        foreach ($files as $file) {
            $this->visitor->setCurrentFile($file);
            $io->writeln(sprintf('Parsing file %s', $file->getPathname()), OutputInterface::VERBOSITY_DEBUG);
            $nodes = $this->parser->parse($file->getContents(), $this->errorHandler) ?? [];

            if (!$nodes) {
                $io->writeln(
                    sprintf('Could not parse nodes from file %s', $file->getFilename()),
                    OutputInterface::VERBOSITY_DEBUG
                );

                $io->progressAdvance();

                continue;
            }

            $traverser->traverse($nodes);
            $io->progressAdvance();
        }

        $io->progressFinish();

        return $this->visitor->getUsages();
    }

}
