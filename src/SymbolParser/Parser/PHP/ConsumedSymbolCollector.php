<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP;

use ComposerUnused\SymbolParser\Parser\PHP\Strategy\StrategyInterface;
use ComposerUnused\SymbolParser\Symbol\SymbolName;
use PhpParser\Node;

use function array_merge;
use function array_unique;

/**
 * Collect consumed symbols.
 *
 * Consumed symbols, are symbols used by your code.
 *
 * These might be classes, functions or constants
 */
class ConsumedSymbolCollector extends AbstractCollector
{
    /** @var array<string> */
    protected array $symbols = [];
    /** @var array<StrategyInterface> */
    protected array $strategies;

    /**
     * @param array<StrategyInterface> $strategies
     */
    public function __construct(array $strategies)
    {
        foreach ($strategies as $strategy) {
            if ($strategy instanceof StrategyInterface) {
                $this->strategies[] = $strategy;
            }
        }
    }

    public function enterNode(Node $node)
    {
        $symbols = [];

        $this->followIncludes($node);

        foreach ($this->strategies as $strategy) {
            if (!$strategy->canHandle($node)) {
                continue;
            }

            $symbols[] = $strategy->extractSymbolNames($node);
        }

        if (count($symbols) > 0) {
            $this->symbols = array_merge($this->symbols, ...$symbols);
        }

        return null;
    }

    public function getSymbolNames(): array
    {
        $uniqueNames = array_map(
            static fn(string $name) => new SymbolName($name),
            array_unique($this->symbols)
        );

        $symbolNames = [];

        foreach ($uniqueNames as $symbolName) {
            foreach ($uniqueNames as $otherSymbolName) {
                if ($symbolName === $otherSymbolName) {
                    continue;
                }

                if ($symbolName->isPartOf($otherSymbolName)) {
                    $mergedSymbol = $otherSymbolName->merge($symbolName);
                    $symbolNames[$mergedSymbol->getName()] = $mergedSymbol;
                    continue 2;
                }

                if ($otherSymbolName->isPartOf($symbolName)) {
                    $mergedSymbol = $symbolName->merge($otherSymbolName);
                    $symbolNames[$mergedSymbol->getName()] = $mergedSymbol;
                    continue 2;
                }
            }

            $symbolNames[$symbolName->getName()] = $symbolName;
        }

        return array_keys($symbolNames);
    }

    public function reset(): void
    {
        $this->symbols = [];
    }
}
