<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Loader\Filter;

use Composer\Package\Link;
use Icanhazstring\Composer\Unused\Loader\Filter\ExcludePackageFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ExcludeFilterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function itShouldValidateFilterDataProvider(): array
    {
        return [
            'package not in exclude should not match' => [
                'expected' => false,
                'excludes' => [],
            ],
            'package found in excludes should match'  => [
                'expected' => true,
                'excludes' => ['package/A'],
            ]
        ];
    }

    /**
     * @param bool  $expected
     * @param array<string> $excludes
     * @return void
     * @test
     * @dataProvider itShouldValidateFilterDataProvider
     */
    public function itShouldValidateFilter(bool $expected, array $excludes): void
    {
        $link = $this->prophesize(Link::class);
        $link->getTarget()->willReturn('package/A');

        $filter = new ExcludePackageFilter($excludes);
        $this->assertSame($expected, $filter->match($link->reveal()));
    }
}
