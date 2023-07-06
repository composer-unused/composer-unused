<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Unit\Configuration;

use ComposerUnused\ComposerUnused\Configuration\AdditionalFilesAlreadySetException;
use ComposerUnused\ComposerUnused\Configuration\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldSetAdditionalFiles(): void
    {
        $config = new Configuration();
        $config->setAdditionalFilesFor('test/dependency', ['file1.php']);

        $files = $config->getAdditionalFilesFor('test/dependency');
        expect($files)->notToBeEmpty();
        expect($files[0])->toBe('file1.php');
    }

    /**
     * @test
     */
    public function itShouldNotOverwriteAdditionalFiles(): void
    {
        $config = new Configuration();
        $config->setAdditionalFilesFor('test/dependency', ['file1.php']);

        self::expectException(AdditionalFilesAlreadySetException::class);
        self::expectExceptionMessage('You already added files for test/dependency. Did you want to add multiple files? Try adding these via multiple globs.');
        $config->setAdditionalFilesFor('test/dependency', ['file2.php']);
    }
}
