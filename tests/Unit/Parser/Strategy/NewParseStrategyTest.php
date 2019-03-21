<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Parser\Strategy;

use Icanhazstring\Composer\Unused\Parser\Strategy\NewParseStrategy;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PHPUnit\Framework\TestCase;

class NewParseStrategyTest extends TestCase
{
    /**
     * @test
     * @dataProvider itShouldMatchCertainValuesDataProvider
     * @param bool   $expected
     * @param string $className
     * @param bool   $isFullyQualified
     * @return void
     */
    public function itShouldMatchCertainValues(bool $expected, string $className, bool $isFullyQualified): void
    {
        $class = $this->prophesize(Name::class);
        $class->isFullyQualified()->willReturn($isFullyQualified);
        $new = new $className($class->reveal());

        $strategy = new NewParseStrategy();

        $this->assertEquals($expected, $strategy->meetsCriteria($new));
    }

    /**
     * @return array
     */
    public function itShouldMatchCertainValuesDataProvider(): array
    {
        return [
            'FullyQualifiedCorrectNode'    => [true, New_::class, true],
            'FullyQualifiedWrongNode'      => [false, Variable::class, true],
            'NotFullyQualifiedCorrectNode' => [false, New_::class, false],
            'NotFullyQualifiedWrongNode'   => [false, Variable::class, false]
        ];
    }
}
