<?php

namespace Icanhazstring\Composer\Test\Unused\Unit\Loader;

use Composer\Package\Link;
use Composer\Semver\Constraint\ConstraintInterface;
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
        $constraint = $this->prophesize(ConstraintInterface::class)->reveal();

        return [
            [new Link('', 'php', $constraint)],
            [new Link('', 'ext-php', $constraint)],
            [new Link('', 'ext-json', $constraint)],
        ];
    }

    /**
     * @return array<array<Link>>
     */
    public function invalidPhpExtensionDataProvider(): array
    {
        $constraint = $this->prophesize(ConstraintInterface::class)->reveal();

        return [
            [new Link('', 'json-ext', $constraint)],
            [new Link('', 'php7', $constraint)],
            [new Link('', 'Package/Name', $constraint)],
            [new Link('', '', $constraint)],
        ];
    }
}
