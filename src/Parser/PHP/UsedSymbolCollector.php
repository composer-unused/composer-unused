<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP;

use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\StrategyInterface;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

use function array_merge;
use function array_unique;

class UsedSymbolCollector extends NodeVisitorAbstract implements SymbolCollectorInterface
{
    /** @var array<string> */
    private $symbols = [];
    /** @var array<StrategyInterface> */
    private $strategies;

    /**
     * @param array<StrategyInterface> $strategies
     */
    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function enterNode(Node $node)
    {
        $symbols = [];

        foreach ($this->strategies as $strategy) {
            if (!$strategy->canHandle($node)) {
                continue;
            }

            $symbols[] = $strategy->extractSymbolNames($node);
        }

        if (count($symbols) !== 0) {
            $this->symbols = array_merge($this->symbols, ...$symbols);
        }

        return null;
    }

    public function getSymbolNames(): array
    {
        return array_unique($this->symbols);
    }
}
