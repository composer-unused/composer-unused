<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser;

use Icanhazstring\Composer\Unused\Parser\Strategy\ParseStrategyInterface;
use Icanhazstring\Composer\Unused\Subject\NamespaceUsage;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SplFileInfo;

class NodeVisitor extends NodeVisitorAbstract
{
    /** @var ParseStrategyInterface[] */
    private $strategies;
    /** @var UsageInterface[] */
    private $usages;
    /** @var SplFileInfo */
    private $currentFile;

    /**
     * @param ParseStrategyInterface[] $strategies
     */
    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function enterNode(Node $node)
    {
        foreach ($this->strategies as $strategy) {
            if (!$strategy->meetsCriteria($node)) {
                continue;
            }

            $name = $strategy->extractNamespace($node);
            $this->usages[$name] = new NamespaceUsage($this->currentFile, $name, $node);
        }
    }

    /**
     * @return UsageInterface[]
     */
    public function getUsages(): array
    {
        return $this->usages;
    }

    public function setCurrentFile(SplFileInfo $file): void
    {
        $this->currentFile = $file;
    }
}
