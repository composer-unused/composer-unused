<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Configuration\ConfigurationSet;

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\ConfigurationSetInterface;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

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
                $phpFiles = $this->findPhpFiles($dirPath);
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

    /**
     * Find all PHP files recursively in a directory
     *
     * @param string $directory
     * @return array<string>
     */
    private function findPhpFiles(string $directory): array
    {
        $phpFiles = [];

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            );

            foreach ($iterator as $file) {
                if ($file instanceof SplFileInfo && $file->isFile() && $file->getExtension() === 'php') {
                    $phpFiles[] = $file->getPathname();
                }
            }
        } catch (\Exception) {
            // Directory not readable or other error - return empty array
        }

        return $phpFiles;
    }

    protected function resolvePath(string $path): string|false
    {
        return realpath($path);
    }
}
