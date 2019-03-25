<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Subject;

use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Subject\PackageSubject;
use PHPUnit\Framework\TestCase;

class PackageSubjectTest extends TestCase
{
    public function itShouldReturnProperResultForPackageAutoloadDataProvider(): array
    {
        return [
            'no psr-4 namespaces'                 => [
                'expected'        => false,
                'usedNamespace'       => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => []
            ],
            'single matching psr-4 namespace'     => [
                'expected'        => true,
                'usedNamespace'       => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => [
                    'psr-4' => [
                        'Test\\Namespace\\Package\\' => 'fu/bar/path'
                    ]
                ]
            ],
            'single non matching psr-4 namespace' => [
                'expected'        => false,
                'usedNamespace'       => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => [
                    'psr-4' => [
                        'FailTest\\Namespace\\Package\\' => 'fu/bar/path'
                    ]
                ]
            ],
            'multi matching psr-4 namespace'      => [
                'expected'        => true,
                'usedNamespace'       => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => [
                    'psr-4' => [
                        'Test\\Namespace\\Package\\'          => 'fu/bar/path',
                        'Another\\Test\\Namespace\\Package\\' => 'fu/bar/path/2'
                    ]
                ]
            ],
            'multi non matching psr-4 namespace'  => [
                'expected'        => false,
                'usedNamespace'       => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => [
                    'psr-4' => [
                        'FailTest\\Namespace\\Package\\'      => 'fu/bar/path',
                        'Another\\Test\\Namespace\\Package\\' => 'fu/bar/path/2'
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider itShouldReturnProperResultForPackageAutoloadDataProvider
     *
     * @param bool   $exptected
     * @param string $usedNamespace
     * @param array  $packageAutoload
     * @return void
     */
    public function itShouldReturnProperResultForPackageAutoload(
        bool $exptected,
        string $usedNamespace,
        array $packageAutoload
    ): void {
        $composerPackage = $this->prophesize(PackageInterface::class);
        $composerPackage->getAutoload()->willReturn($packageAutoload);

        $subject = new PackageSubject($composerPackage->reveal());
        $this->assertEquals($exptected, $subject->providesNamespace($usedNamespace));
    }
}
