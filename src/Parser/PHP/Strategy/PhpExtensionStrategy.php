<?php

namespace Icanhazstring\Composer\Unused\Parser\PHP\Strategy;

use PhpParser\Node;
use Psr\Log\LoggerInterface;
use ReflectionException;
use ReflectionExtension;
use ReflectionFunction;

class PhpExtensionStrategy implements StrategyInterface
{
    /** @var array<string, array<string, int>> */
    private $extensionConstants = [];

    /** @var array<string, array<ReflectionFunction>> */
    private $extensionFunctions = [];

    /** @var array<string, array<string, int>> */
    private $extensionClasses = [];

    /**
     * @param array<string> $extensions
     * @throws ReflectionException
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
        $searchingName = $this->getNameFromNode($node);

        $matches = [];
        $extensions = [
            $this->extensionClasses,
            $this->extensionFunctions,
            $this->extensionConstants
        ];
        foreach ($extensions as $type) {
            foreach ($type as $phpextension => $extensionClassFuncOrConst) {
                if (array_key_exists($searchingName, $extensionClassFuncOrConst)) {
                    if ($phpextension === 'Core') {
                        $matches[] = 'php';
                    } else {
                        $matches[] = 'ext-' . str_replace(' ', '-', strtolower($phpextension));
                    }
                }
            }
        }

        return $matches;
    }

    private function getNameFromNode(Node $node): string
    {
        if ($node instanceof Node\Name\FullyQualified) {
            return implode('\\', $node->parts);
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
