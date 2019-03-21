<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Parser\Strategy;

use Icanhazstring\Composer\Unused\Parser\Strategy\UseParseStrategy;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PHPUnit\Framework\TestCase;

class UseParseStrategyTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function itShouldParseUseImports(): void
    {
        $useUseName = $this->prophesize(Name::class);
        $useUseName->toString()->shouldBeCalled()->willReturn('Namespace\Class');
        $useUse = new UseUse($useUseName->reveal());
        $use = new Use_([$useUse]);

        $strategy = new UseParseStrategy();
        $this->assertTrue($strategy->meetsCriteria($use));
        $this->assertEquals('Namespace\\Class', $strategy->extractNamespace($use));
    }
}
