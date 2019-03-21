<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Parser\Strategy;

use Icanhazstring\Composer\Unused\Parser\Strategy\StaticParseStrategy;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PHPUnit\Framework\TestCase;

class StaticParseStrategyTest extends TestCase
{
    /**
     * @dataProvider itShouldMatchForCorrectNodesDataProvider
     * @test
     * @param bool $expected
     * @param string $class
     * @param bool $isFullyQualified
     * @return void
     */
    public function itShouldMatchForCorrectNodes(bool $expected, string $class, bool $isFullyQualified): void
    {
        $name = $this->prophesize(Name::class);
        $name->isFullyQualified()->willReturn($isFullyQualified);
        $expr = new $class($name->reveal(), $name->reveal());
        $node = new Expression($expr);

        $strategy = new StaticParseStrategy();
        $this->assertEquals($expected, $strategy->meetsCriteria($node));
    }

    /**
     * @return array
     */
    public function itShouldMatchForCorrectNodesDataProvider(): array
    {
        return [
            'FullyQualifiedNameCorrectType' => [true, StaticCall::class, true],
            'NotFullyQualifiedNameCorrectType' => [false, StaticCall::class, false],
            'FullyQualifiedNameWrongType' => [false, StaticPropertyFetch::class, true],
            'NotFullyQualifiedNameWrongType' => [false, StaticPropertyFetch::class, false]
        ];
    }
}
