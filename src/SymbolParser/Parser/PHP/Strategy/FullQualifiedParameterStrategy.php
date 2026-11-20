<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;

final class FullQualifiedParameterStrategy implements StrategyInterface
{
    public function canHandle(Node $node): bool
    {
        if (!$node instanceof Node\Stmt\ClassMethod) {
            return false;
        }

        return count($node->params) > 0;
    }

    /**
     * @param Node&Node\Stmt\ClassMethod $node
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        /** @var array<Node\Param> $params */
        $params = $node->params;

        $typedParams = array_filter($params, static function (Node\Param $param) {
            return $param->type instanceof FullyQualified;
        });

        return array_map(static function (Node\Param $param) {
            /** @var FullyQualified $type */
            $type = $param->type;
            return $type->toString();
        }, $typedParams);
    }
}
