<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

use function array_merge;
use function join;

class ForeignSymbolCollector extends NodeVisitorAbstract implements SymbolCollectorInterface
{
    /** @var string */
    private $namespace = '';

    /** @var array<string> */
    private $functions = [];
    /** @var array<string> */
    private $constants = [];
    /** @var array<string> */
    private $classes = [];

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->namespace = $node->name . '\\';
            return null;
        }

        if ($node instanceof Node\Stmt\Class_) {
            $this->classes[] = $this->namespace . $node->name;

            // We only need the class name, no need to dig further into the class
            // as there is no more symbol to be defined which can't be checked against
            // the class name already (e.g. public constants)
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($node instanceof Node\Stmt\Function_) {
            $this->functions[] = $this->namespace . $node->name;
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($node instanceof Node\Const_) {
            $this->constants[] = $this->namespace . $node->name;
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        return null;
    }

    public function reset(): void
    {
        $this->classes = [];
        $this->constants = [];
        $this->functions = [];
        $this->namespace = '';
    }

    public function getSymbolNames(): array
    {
        return array_merge(
            $this->classes,
            $this->functions,
            $this->constants
        );
    }
}
