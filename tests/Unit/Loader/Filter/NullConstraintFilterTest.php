<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Loader\Filter;

use Composer\Package\Link;
use Icanhazstring\Composer\Unused\Loader\Filter\NullConstraintFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class NullConstraintFilterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function itShouldValidateFilterDataProvider(): array
    {
        $noConstraintLink = $this->prophesize(Link::class);
        $noConstraintLink->getConstraint()->willReturn(null);

        $constraintLink = $this->prophesize(Link::class);
        $constraintLink->getConstraint()->willReturn('^0.1');

        return [
            'link with no constraint should match'  => [
                'expected' => true,
                'link'     => $noConstraintLink->reveal()
            ],
            'link with constraint should not match' => [
                'expected' => false,
                'link'     => $constraintLink->reveal()
            ]
        ];
    }

    /**
     * @param bool $expected
     * @param Link $link
     * @return void
     * @test
     * @dataProvider itShouldValidateFilterDataProvider
     */
    public function itShouldValidateFilter(bool $expected, Link $link): void
    {
        $filter = new NullConstraintFilter();
        $this->assertSame($expected, $filter->match($link));
    }
}
