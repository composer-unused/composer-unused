<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Configuration\ConfigurationSet;

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\ConfigurationSetInterface;
use Webmozart\Glob\Glob;

class SymfonyConfigurationSet implements ConfigurationSetInterface
{
    private string $projectRoot;
    private string $rootPackageName;

    public function __construct(string $rootPackageName, string $projectRoot = '.')
    {
        $this->rootPackageName = $rootPackageName;
        $this->projectRoot = rtrim($projectRoot, '/');
    }

    public function apply(Configuration $configuration): Configuration
    {
        $symfonyFiles = $this->getSymfonyFiles();

        if (empty($symfonyFiles)) {
            return $configuration;
        }

        // Add additional files for the root package (current project)
        $existingFiles = $configuration->getAdditionalFilesFor($this->rootPackageName);
        if (empty($existingFiles)) {
            $configuration->setAdditionalFilesFor($this->rootPackageName, $symfonyFiles);
        }

        return $configuration;
    }

    public function getName(): string
    {
        return 'symfony';
    }

    public function getDescription(): string
    {
        return 'Adds common Symfony project directories (bin/, config/, public/, assets/, migrations/) for symbol scanning';
    }

    /**
     * Get all PHP files from Symfony directories
     *
     * @return array<string>
     */
    private function getSymfonyFiles(): array
    {
        $allFiles = [];
        $symfonyDirs = [
            'bin',        // Console commands and executables
            'config',     // Configuration files that may use dependencies
            'public',     // Web entry point (index.php)
            'assets',     // Frontend assets (may contain PHP)
            'migrations', // Database migrations
        ];

        foreach ($symfonyDirs as $dir) {
            $dirPath = $this->resolvePath($this->projectRoot . '/' . $dir);
            if ($dirPath !== false && is_dir($dirPath)) {
                $phpFiles = Glob::glob($dirPath . '/**/*.php');
                $allFiles = array_merge($allFiles, $phpFiles);
            }
        }

        // Add bin/console directly (doesn't have .php extension)
        $consolePath = $this->resolvePath($this->projectRoot . '/bin/console');
        if ($consolePath !== false && file_exists($consolePath)) {
            $allFiles[] = $consolePath;
        }

        return $allFiles;
    }

    protected function resolvePath(string $path): string|false
    {
        return realpath($path);
    }
}
