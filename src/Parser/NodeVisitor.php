<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser;

use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\Strategy\ParseStrategyInterface;
use Icanhazstring\Composer\Unused\Subject\NamespaceUsage;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SplFileInfo;
use Throwable;

class NodeVisitor extends NodeVisitorAbstract
{
    /** @var ParseStrategyInterface[] */
    private $strategies;
    /** @var UsageInterface[] */
    private $usages = [];
    /** @var SplFileInfo */
    private $currentFile;
    /** @var ErrorHandlerInterface */
    private $errorHandler;

    /**
     * @param ParseStrategyInterface[] $strategies
     * @param ErrorHandlerInterface    $errorHandler
     */
    public function __construct(array $strategies, ErrorHandlerInterface $errorHandler)
    {
        $this->strategies = $strategies;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @param Node $node
     * @return int|Node|void|null
     * @throws Throwable
     */
    public function enterNode(Node $node)
    {
        foreach ($this->strategies as $strategy) {
            try {
                if (!$strategy->meetsCriteria($node)) {
                    continue;
                }

                $namespaces = $strategy->extractNamespaces($node);

                foreach ($namespaces as $namespace) {
                    $this->usages[$namespace] = new NamespaceUsage($this->currentFile, $namespace, $node);
                }
            } catch (Throwable $error) {
                $this->errorHandler->handle($error);
            }
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
