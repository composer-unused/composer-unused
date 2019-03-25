<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser;

use Icanhazstring\Composer\Unused\Parser\Strategy\ParseStrategyInterface;
use Icanhazstring\Composer\Unused\Subject\NamespaceUsage;
use Icanhazstring\Composer\Unused\Subject\UsageInterface;
use PhpParser\ErrorHandler;
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
    /** @var ErrorHandler */
    private $errorHandler;

    /**
     * @param ParseStrategyInterface[] $strategies
     * @param ErrorHandler|null        $errorHandler
     */
    public function __construct(array $strategies, ErrorHandler $errorHandler = null)
    {
        $this->strategies = $strategies;
        $this->errorHandler = $errorHandler ?? new ErrorHandler\Throwing();
    }

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
                $this->errorHandler->handleError($error);
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
