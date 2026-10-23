<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\Composer;

use ComposerUnused\ComposerUnused\Composer\PackageUrlExtractor;
use PHPUnit\Framework\TestCase;

final class PackageUrlExtractorTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsNullForPackagesWithoutSourceOrDist(): void
    {
        $extractor = new PackageUrlExtractor();

        self::assertNull($extractor->getUrl(['name' => 'vendor/meta']));
    }

    /**
     * @test
     */
    public function itReturnsDistUrlWhenSourceIsMissing(): void
    {
        $extractor = new PackageUrlExtractor();

        self::assertSame('https://example.com/package.zip', $extractor->getUrl([
            'name' => 'vendor/package',
            'dist' => ['url' => 'https://example.com/package.zip'],
        ]));
    }

    /**
     * @test
     */
    public function itPrefersTheSourceUrlWhenAvailable(): void
    {
        $extractor = new PackageUrlExtractor();

        self::assertSame('https://example.com/package', $extractor->getUrl([
            'name' => 'vendor/package',
            'source' => ['url' => 'https://example.com/package.git'],
            'dist' => ['url' => 'https://example.com/package.zip'],
        ]));
    }
}
