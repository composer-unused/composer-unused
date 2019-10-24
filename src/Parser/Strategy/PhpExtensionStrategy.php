<?php

namespace Icanhazstring\Composer\Unused\Parser\Strategy;

use PhpParser\Node;

class PhpExtensionStrategy implements ParseStrategyInterface
{
    /** @var array<string, mixed> */
    private $extensionConstants = [];

    /** @var array<string, mixed> */
    private $extensionFunctions = [];

    /** @var array<string, mixed> */
    private $extensionClasses = [];

    public function __construct(array $extensions)
    {
        foreach ($extensions as $extension) {
            $reflection = new \ReflectionExtension($extension);
            $this->extensionConstants[$extension] = $reflection->getConstants();
            $this->extensionFunctions[$extension] = $reflection->getFunctions();
            $this->extensionClasses[$extension] = array_flip($reflection->getClassNames());
        }
    }

    public function meetsCriteria(Node $node): bool
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

    public function extractNamespaces(Node $node): array
    {
        $searchingName = $this->getNameFromNode($node);

        $matches = [];
        foreach (
            [
            $this->extensionClasses,
            $this->extensionFunctions,
            $this->extensionConstants
            ] as $type
        ) {
            foreach ($type as $phpextension => $extensionConstant) {
                if (array_key_exists($searchingName, $extensionConstant)) {
                    $matches[] = $phpextension;
                }
            }
        }
        return $matches;
    }

    private function getNameFromNode(Node $node): string
    {
        if ($node instanceof Node\Name\FullyQualified) {
            return $node->parts[0];
        }
        if ($node instanceof Node\Stmt\UseUse) {
            return $node->name->parts[0];
        }

        if ($node instanceof Node\Expr\ConstFetch) {
            return $node->name->parts[0];
        }

        if ($node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name) {
            return $node->name->parts[0];
        }

        return '';
    }
}
