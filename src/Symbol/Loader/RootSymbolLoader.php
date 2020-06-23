<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Composer\Package\PackageInterface;
use Generator;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class RootSymbolLoader implements SymbolLoaderInterface
{
    /** @var string */
    private $rootDir;
    /** @var array */
    private $excludes;

    public function __construct(
        Parser $parser,
        string $rootDir,
        array $excludes
    ) {
        $this->rootDir = $rootDir;
        $this->excludes = $excludes;
    }

    public function load(PackageInterface $package): Generator
    {
        $finder = new Finder();

        /** @var SplFileInfo[] $files */
        $files = $finder
            ->files()
            ->name('*.php')
            ->in($this->rootDir)
            ->exclude(
                array_merge(['vendor'], $this->excludes)
            );

        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->visitor);

        foreach ($files as $file) {
            $nodes = $this->parser->parse($file->getContents()) ?? [];

            if (!$nodes) {
                continue;
            }

            $traverser->traverse($nodes);
        }
    }
}
