<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\Filter;

use ComposerUnused\ComposerUnused\Filter\PatternFilter;
use ComposerUnused\ComposerUnused\Test\Stubs\TestDependency;
use PHPUnit\Framework\TestCase;

final class PatternFilterTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAlwaysBeUsed(): void
    {
        $filter = new PatternFilter('/test/', true);
        $dependency = new TestDependency('fubar');

        self::assertFalse($filter->applies($dependency), 'Dependency named "fubar" should not apply to pattern "/test/"');
        self::assertTrue($filter->used(), 'Filter should remain true');
    }

    /**
     * @test
     */
    public function itShouldMarkFilterAsUsed(): void
    {
        $filter = new PatternFilter('/test/');
        $dependency = new TestDependency('test');

        self::assertTrue($filter->applies($dependency), 'dependency named "test" should apply to pattern "/test/"');
        self::assertTrue($filter->used());
    }

    /**
     * @test
     */
    public function itShouldRemainUnused(): void
    {
        $filter = new PatternFilter('/test/');
        $dependency = new TestDependency('fubar');

        self::assertFalse($filter->applies($dependency), 'dependency named "fubar" should not apply to pattern "/test/"');
        self::assertFalse($filter->used());
    }
}
