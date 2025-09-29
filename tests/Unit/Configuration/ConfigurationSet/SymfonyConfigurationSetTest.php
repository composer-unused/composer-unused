<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\Configuration\ConfigurationSet;

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\ConfigurationSet\SymfonyConfigurationSet;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SymfonyConfigurationSetTest extends TestCase
{
    private vfsStreamDirectory $vfsRoot;

    protected function setUp(): void
    {
        $this->vfsRoot = vfsStream::setup('project');
    }

    public function testApplyAddsSymfonyDirectoriesToConfiguration(): void
    {
        // Create a mock Symfony project structure
        vfsStream::create([
            'bin' => [
                'console' => '<?php // console file'
            ],
            'config' => [
                'services.yaml' => 'services: []',
                'packages' => [
                    'framework.yaml' => 'framework: []'
                ]
            ],
            'public' => [
                'index.php' => '<?php // entry point'
            ],
            'migrations' => [
                'Version20231201000000.php' => '<?php // migration'
            ]
        ], $this->vfsRoot);

        $configuration = new Configuration();
        /** @var SymfonyConfigurationSet&MockObject $configurationSet */
        $configurationSet = $this->getMockBuilder(SymfonyConfigurationSet::class)
            ->setConstructorArgs(['my/project', $this->vfsRoot->url()])
            ->onlyMethods(['resolvePath'])
            ->getMock();
        $configurationSet->method('resolvePath')
            ->willReturnCallback(static function (string $path) {
                return $path;
            });

        $result = $configurationSet->apply($configuration);

        $additionalFiles = $result->getAdditionalFilesFor('my/project');

        $this->assertNotEmpty($additionalFiles);
        $this->assertContains($this->vfsRoot->url() . '/bin/console', $additionalFiles);
        $this->assertContains($this->vfsRoot->url() . '/public/index.php', $additionalFiles);
        $this->assertContains($this->vfsRoot->url() . '/migrations/Version20231201000000.php', $additionalFiles);
    }

    public function testApplyDoesNotOverrideExistingAdditionalFiles(): void
    {
        vfsStream::create([
            'bin' => [
                'console' => '<?php // console file'
            ]
        ], $this->vfsRoot);

        $configuration = new Configuration();
        $configuration->setAdditionalFilesFor('my/project', ['/existing/file.php']);

        $configurationSet = new SymfonyConfigurationSet('my/project', $this->vfsRoot->url());
        $result = $configurationSet->apply($configuration);

        $additionalFiles = $result->getAdditionalFilesFor('my/project');

        $this->assertEquals(['/existing/file.php'], $additionalFiles);
    }

    public function testApplyWithNonExistentDirectories(): void
    {
        // No directories created - project root is empty
        $configuration = new Configuration();
        $configurationSet = new SymfonyConfigurationSet('my/project', $this->vfsRoot->url());

        $result = $configurationSet->apply($configuration);

        $additionalFiles = $result->getAdditionalFilesFor('my/project');
        $this->assertEmpty($additionalFiles);
    }

    public function testGetName(): void
    {
        $configurationSet = new SymfonyConfigurationSet('my/project');
        $this->assertEquals('symfony', $configurationSet->getName());
    }

    public function testGetDescription(): void
    {
        $configurationSet = new SymfonyConfigurationSet('my/project');
        $this->assertStringContainsString('Symfony project directories', $configurationSet->getDescription());
    }
}
