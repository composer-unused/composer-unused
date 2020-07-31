<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Parser\PHP;

use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;

class SymbolNodeVisitorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldNotTraverseClasses(): void
    {
        $visitor = new ForeignSymbolCollector();
        $node = new Class_('test');

        self::assertSame(NodeTraverser::DONT_TRAVERSE_CHILDREN, $visitor->enterNode($node));
    }

    /**
     * @test
     */
    public function itShouldAddFunctionSymbolNames(): void
    {
        $visitor = new ForeignSymbolCollector();
        $node = new Function_('Testfunction');

        $visitor->enterNode($node);

        self::assertCount(1, $visitor->getSymbolNames());
        self::assertContains('Testfunction', $visitor->getSymbolNames());
    }

    /**
     * @test
     */
    public function itShouldAddConstantSymbolNames(): void
    {
        $visitor = new ForeignSymbolCollector();
        $node = new Const_('Testconst', new String_('Conststring'));

        $visitor->enterNode($node);

        self::assertCount(1, $visitor->getSymbolNames());
        self::assertContains('Testconst', $visitor->getSymbolNames());
    }
}
