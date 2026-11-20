<?php

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;

/**
 * Strategy to use with ConsumedSymbolCollector to replace usage of DefinedSymbolCollector.
 *
 * @link https://github.com/composer-unused/symbol-parser/discussions/137
 * @author Laurent Laville
 */
final class DefineStrategy implements StrategyInterface
{
    private string $namespace = '';

    public function canHandle(Node $node): bool
    {
        if (
            $node instanceof Node\Stmt\Namespace_ ||
            $node instanceof Node\Stmt\ClassLike ||
            $node instanceof Node\Stmt\Function_ ||
            $node instanceof Node\Const_
        ) {
            return (null !== $node->name);
        }
        if ($node instanceof Node\Stmt\Expression && $node->expr instanceof Node\Expr\FuncCall) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function extractSymbolNames(Node $node): array
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->namespace = $node->name . '\\';
            return [$node->name ? $node->name->toString() : ''];
        }

        if ($node instanceof Node\Stmt\ClassLike) {
            $classLike = $this->namespace . $node->name;
            return [$classLike];
        }

        if ($node instanceof Node\Stmt\Function_) {
            $function = $this->namespace . $node->name;
            return [$function];
        }

        if ($node instanceof Node\Const_) {
            $constant = $this->namespace . $node->name;
            return [$constant];
        }

        if (
            $node instanceof Node\Stmt\Expression &&
            $node->expr instanceof Node\Expr\FuncCall &&
            $node->expr->name instanceof Node\Name
        ) {
            /** @var Node\Name $expressionName */
            $expressionName = $node->expr->name;
            $functionName = $expressionName->getParts()[0];
            $firstArgument = $node->expr->args[0];
            if ($firstArgument instanceof Node\Arg) {
                $firstArgumentValue = $firstArgument->value;
                if ($functionName === 'define' && $firstArgumentValue instanceof Node\Scalar\String_) {
                    $constant = $firstArgumentValue->value;
                    return [$constant];
                }
            }
        }

        return [];
    }
}
