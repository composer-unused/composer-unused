<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;

final class FunctionInvocationStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        if (!$node instanceof FuncCall) {
            return false;
        }

        if (!$node->name instanceof Node\Name) {
            return false;
        }

        return true;
    }

    /**
     * @param Node&FuncCall $node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        $nodeName = $node->name;
        assert($nodeName instanceof Node\Name);

        return [$nodeName->toString()];
    }
}
