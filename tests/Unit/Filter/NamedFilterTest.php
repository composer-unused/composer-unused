<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\Filter;

use ComposerUnused\ComposerUnused\Filter\NamedFilter;
use ComposerUnused\ComposerUnused\Test\Stubs\TestDependency;
use PHPUnit\Framework\TestCase;

final class NamedFilterTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAlwaysBeUsed(): void
    {
        $filter = new NamedFilter('test', true);
        self::assertTrue($filter->used());
    }

    /**
     * @test
     */
    public function itShouldMarkFilterAsUsed(): void
    {
        $filter = new NamedFilter('test');
        $dependency = new TestDependency('test');

        self::assertTrue($filter->applies($dependency), 'dependency named "test" should apply to named filter "test"');
        self::assertTrue($filter->used());
    }

    /**
     * @test
     */
    public function itShouldRemainUnused(): void
    {
        $filter = new NamedFilter('test');
        $dependency = new TestDependency('fubar');

        self::assertFalse($filter->applies($dependency), 'dependency named "fubar" should not apply to named filter "test"');
        self::assertFalse($filter->used());
    }
}
