<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Psr\Log\LoggerInterface;
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
    /** @var LoggerInterface */
    private $debugLogger;

    public function __construct(
        Parser $parser,
        NodeVisitor $visitor,
        ErrorHandlerInterface $errorHandler,
        LoggerInterface $debugLogger
    ) {
        $this->parser = $parser;
        $this->visitor = $visitor;
        $this->errorHandler = $errorHandler;
        $this->debugLogger = $debugLogger;
    }

    /**
     * @param Composer     $composer
     * @param SymfonyStyle $io
     *
     * @return UsageInterface[]
     */
    public function load(Composer $composer, SymfonyStyle $io): array
    {
        $finder = new Finder();
        $baseDir = dirname($composer->getConfig()->getConfigSource()->getName());

        /** @var SplFileInfo[] $files */
        $files = $finder
            ->files()
            ->name('*.php')
            ->in($baseDir)
            ->exclude(['vendor']);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->visitor);

        $io->section(sprintf('Scanning files from basedir %s', $baseDir));

        $io->progressStart(count($files));

        foreach ($files as $file) {
            $this->visitor->setCurrentFile($file);
            $this->debugLogger->debug(sprintf('Parsing file %s', $file->getPathname()));

            $nodes = $this->parser->parse($file->getContents(), $this->errorHandler) ?? [];

            if (!$nodes) {
                $this->debugLogger->debug(sprintf('Could not parse nodes from file %s', $file->getFilename()));
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
