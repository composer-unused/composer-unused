<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\NodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UsageLoader implements LoaderInterface
{
    use PrograssBarTrait;

    /** @var Parser */
    private $parser;
    /** @var NodeVisitor */
    private $visitor;
    /** @var ErrorHandlerInterface */
    private $errorHandler;
    /** @var LoggerInterface */
    private $logger;
    /** @var ResultInterface */
    private $loaderResult;
    /** @var array<string> */
    private $excludes;

    /**
     * @param array<string> $excludes
     */
    public function __construct(
        Parser $parser,
        NodeVisitor $visitor,
        ErrorHandlerInterface $errorHandler,
        LoggerInterface $logger,
        ResultInterface $loaderResult,
        array $excludes = []
    ) {
        $this->parser = $parser;
        $this->visitor = $visitor;
        $this->errorHandler = $errorHandler;
        $this->logger = $logger;
        $this->loaderResult = $loaderResult;
        $this->excludes = $excludes;
    }

    /**
     * @param Composer     $composer
     * @param SymfonyStyle $io
     * @return ResultInterface
     */
    public function load(Composer $composer, SymfonyStyle $io): ResultInterface
    {
        $this->io = $io;
        $finder = new Finder();
        $baseDir = dirname($composer->getConfig()->getConfigSource()->getName());

        /** @var SplFileInfo[] $files */
        $files = $finder
            ->files()
            ->name('*.php')
            ->in($baseDir)
            ->exclude(
                array_merge(['vendor'], $this->excludes)
            );

        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->visitor);

        $io->section(sprintf('Scanning files from basedir %s', $baseDir));

        $this->progressStart(count($files));

        foreach ($files as $file) {
            $this->progressAdvance();
            $this->visitor->setCurrentFile($file);
            $this->logger->debug(sprintf('Parsing file %s', $file->getPathname()));

            $nodes = $this->parser->parse($file->getContents(), $this->errorHandler) ?? [];

            if (!$nodes) {
                $this->loaderResult->skipItem($file->getFilename(), 'Could not parse nodes');
                $this->logger->debug(sprintf('Could not parse nodes from file %s', $file->getFilename()));

                continue;
            }

            $traverser->traverse($nodes);
        }

        $this->progressFinish();

        foreach ($this->visitor->getUsages() as $usage) {
            $this->loaderResult->addItem($usage);
        }

        return $this->loaderResult;
    }
}
