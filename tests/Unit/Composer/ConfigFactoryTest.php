<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\Composer;

use ComposerUnused\ComposerUnused\Composer\ConfigFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfigFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itValidateNameFromRootConfigPackage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Validation errors: Missing 'name' property in composer.json");

        (new ConfigFactory())
            ->fromPath(__DIR__ . '/../../assets/Config/no_name_package_composer.json');
    }
}
