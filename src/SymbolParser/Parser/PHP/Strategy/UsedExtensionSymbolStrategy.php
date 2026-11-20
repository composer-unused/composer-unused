<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP\Strategy;

use PhpParser\Node;
use Psr\Log\LoggerInterface;
use ReflectionException;
use ReflectionExtension;
use ReflectionFunction;

use function array_key_exists;

final class UsedExtensionSymbolStrategy implements StrategyInterface
{
    /** @var array<string, array<string, int>> */
    private array $extensionConstants = [];

    /** @var array<string, array<ReflectionFunction>> */
    private array $extensionFunctions = [];

    /** @var array<string, array<string, int>> */
    private array $extensionClasses = [];

    /**
     * @param array<string> $extensions
     */
    public function __construct(array $extensions, LoggerInterface $logger)
    {
        foreach ($extensions as $extension) {
            try {
                $reflection = new ReflectionExtension($extension);
                $this->extensionConstants[$extension] = $reflection->getConstants();
                $this->extensionFunctions[$extension] = $reflection->getFunctions();
                $this->extensionClasses[$extension] = array_flip($reflection->getClassNames());
            } catch (ReflectionException $e) {
                $logger->error('Could not parse extension ' . $extension);
            }
        }
    }

    public function canHandle(Node $node): bool
    {
        if ($node instanceof Node\Name\FullyQualified) {
            foreach ($this->extensionClasses as $extensionClass) {
                if (array_key_exists($this->getNameFromNode($node), $extensionClass)) {
                    return true;
                }
            }
        }
        if ($node instanceof Node\Stmt\UseUse) {
            foreach ($this->extensionClasses as $extensionClass) {
                if (array_key_exists($this->getNameFromNode($node), $extensionClass)) {
                    return true;
                }
            }
        }

        if ($node instanceof Node\Expr\ConstFetch) {
            foreach ($this->extensionConstants as $extensionClass) {
                if (array_key_exists($this->getNameFromNode($node), $extensionClass)) {
                    return true;
                }
            }
        }

        if ($node instanceof Node\Expr\FuncCall) {
            foreach ($this->extensionFunctions as $extensionFunction) {
                if (array_key_exists($this->getNameFromNode($node), $extensionFunction)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<string>
     */
    public function extractSymbolNames(Node $node): array
    {
        return [$this->getNameFromNode($node)];
    }

    private function getNameFromNode(Node $node): string
    {
        if ($node instanceof Node\Name\FullyQualified) {
            return implode('\\', $node->getParts());
        }

        if ($node instanceof Node\Stmt\UseUse) {
            return $node->name->getParts()[0];
        }

        if ($node instanceof Node\Expr\ConstFetch) {
            return $node->name->getParts()[0];
        }

        if ($node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name) {
            return $node->name->getParts()[0];
        }

        return '';
    }
}
