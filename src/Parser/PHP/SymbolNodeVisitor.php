<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class SymbolNodeVisitor extends NodeVisitorAbstract
{
    private $functions = [];
    private $constants = [];

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($node instanceof Node\Stmt\Function_) {
            $this->functions[] = (string) $node->name;
        }

        if ($node instanceof Node\Const_) {
            $this->constants[] = (string) $node->name;
        }

        return null;
    }

    public function getFunctionNames(): array
    {
        return $this->functions;
    }

    public function getConstantNames(): array
    {
        return $this->constants;
    }
}
