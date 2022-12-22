<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\Composer;

use ComposerUnused\ComposerUnused\Composer\Config;
use ComposerUnused\ComposerUnused\Composer\PackageFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class PackageFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldFindMatchedLines(): void
    {
        $packageFactory = new PackageFactory();

        $config = new Config();
        $reflection = new ReflectionClass($config);

        $nameProperty = $reflection->getProperty('name');
        $nameProperty->setAccessible(true);
        $nameProperty->setValue($config, 'somename');

        $requirePropery = $reflection->getProperty('require');
        $requirePropery->setAccessible(true);
        $requirePropery->setValue($config, ['test/package' => '0.0']);

        $config->setRaw(<<<JSON
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
