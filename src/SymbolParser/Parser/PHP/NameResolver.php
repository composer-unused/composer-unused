<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP;

use PhpParser\ErrorHandler;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

/**
 * @author Laurent Laville
 */
class NameResolver extends \PhpParser\NodeVisitor\NameResolver
{
    public function __construct(?ErrorHandler $errorHandler = null, array $options = [])
    {
        $errorHandler = $errorHandler ?? new ParserErrorCollector();
        parent::__construct($errorHandler, $options);
        $this->nameContext = new NameContext($errorHandler);
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        $nameContext = $this->getNameContext();

        if ($node instanceof Stmt\Class_) {
            if (null !== $node->name) {
                $name = $this->resolveClassName(new Name($node->name->toString()));
                $nameContext->addAlias($name, 'self', Stmt\Use_::TYPE_NORMAL);
            }

            if (null !== $node->extends) {
                $name = $this->resolveClassName($node->extends);
                $nameContext->addAlias($name, 'parent', Stmt\Use_::TYPE_NORMAL);
            }
        }

        return null;
    }
}
