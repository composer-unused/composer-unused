<?php

namespace Icanhazstring\Composer\Test\Unused\Unit\Loader;

use Composer\Package\Link;
use Icanhazstring\Composer\Unused\Loader\PackageHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PackageHelperTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @dataProvider validPhpExtensionDataProvider
     */
    public function testIsValidPhpExtension(Link $link): void
    {
        $packageHelper = new PackageHelper();
        self::assertTrue($packageHelper->isPhpExtension($link));
    }

    /**
     * @dataProvider invalidPhpExtensionDataProvider
     */
    public function testIsInValidPhpExtension(Link $link): void
    {
        $packageHelper = new PackageHelper();
        self::assertFalse($packageHelper->isPhpExtension($link));
    }

    /**
     * @return array<array<Link>>
     */
    public function validPhpExtensionDataProvider(): array
    {
        return [
            [new Link('', 'php')],
            [new Link('', 'ext-php')],
            [new Link('', 'ext-json')],
        ];
    }

    /**
     * @return array<array<Link>>
     */
    public function invalidPhpExtensionDataProvider(): array
    {
        return [
            [new Link('', 'json-ext')],
            [new Link('', 'php7')],
            [new Link('', 'Package/Name')],
            [new Link('', '')],
        ];
    }
}
