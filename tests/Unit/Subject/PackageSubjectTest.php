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
                'usedNamespace'   => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => []
            ],
            'single matching psr-4 namespace'     => [
                'expected'        => true,
                'usedNamespace'   => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => [
                    'psr-4' => [
                        'Test\\Namespace\\Package\\' => 'fu/bar/path'
                    ]
                ]
            ],
            'single non matching psr-4 namespace' => [
                'expected'        => false,
                'usedNamespace'   => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => [
                    'psr-4' => [
                        'FailTest\\Namespace\\Package\\' => 'fu/bar/path'
                    ]
                ]
            ],
            'multi matching psr-4 namespace'      => [
                'expected'        => true,
                'usedNamespace'   => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => [
                    'psr-4' => [
                        'Test\\Namespace\\Package\\'          => 'fu/bar/path',
                        'Another\\Test\\Namespace\\Package\\' => 'fu/bar/path/2'
                    ]
                ]
            ],
            'multi non matching psr-4 namespace'  => [
                'expected'        => false,
                'usedNamespace'   => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => [
                    'psr-4' => [
                        'FailTest\\Namespace\\Package\\'      => 'fu/bar/path',
                        'Another\\Test\\Namespace\\Package\\' => 'fu/bar/path/2'
                    ]
                ]
            ],
            'multi matching psr-4 dev namespaces' => [
                'expected'        => true,
                'usedNamespace'   => 'Test\\Namespace\\Package\\Model',
                'packageAutoload' => [],
                'packageDevAutoload' => [
                    'psr-4' => [
                        'Test\\Namespace\\Package\\'          => 'fu/bar/path',
                        'Another\\Test\\Namespace\\Package\\' => 'fu/bar/path/2'
                    ]
                ]
            ],
            'empty and valid namespace'           => [
                'expected'           => true,
                'usedNamespace'      => 'Test\\Namespace\\Package\\Model',
                'packageAutoload'    => [
                    'psr-4' => [
                        ''                         => 'fu/bar/path',
                        'Test\\Namespace\\Package' => 'fu/bar/path'
                    ]
                ],
                'packageDevAutoload' => []
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
     * @param array  $packageDevAutoload
     * @return void
     */
    public function itShouldReturnProperResultForPackageAutoload(
        bool $exptected,
        string $usedNamespace,
        array $packageAutoload,
        array $packageDevAutoload = []
    ): void {
        $composerPackage = $this->prophesize(PackageInterface::class);
        $composerPackage->getName()->willReturn('test/package');
        $composerPackage->getVersion()->willReturn('0.1.1');
        $composerPackage->getAutoload()->willReturn($packageAutoload);
        $composerPackage->getDevAutoload()->willReturn($packageDevAutoload);

        $subject = new PackageSubject($composerPackage->reveal());
        $this->assertEquals($exptected, $subject->providesNamespace($usedNamespace));
    }
}
