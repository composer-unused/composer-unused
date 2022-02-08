<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\Composer;

use ComposerUnused\ComposerUnused\Composer\Config;
use ComposerUnused\ComposerUnused\Composer\PackageFactory;
use PHPUnit\Framework\TestCase;

final class PackageFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldFindMatchedLines(): void
    {
        $packageFactory = new PackageFactory();

        $config = $this->createMock(Config::class);
        $config->method('getRequire')->willReturn(
            [
                'test/package' => '0.0'
            ]
        );
        $config->method('getRaw')->willReturn(
            <<<JSON
            {
                "require": {
                    "test/package": "^0.0"
                }
            }
            JSON
        );

        $package = $packageFactory->fromConfig($config);
        self::assertEquals(3, $package->getRequires()[0]->getLineNumber());
    }
}
