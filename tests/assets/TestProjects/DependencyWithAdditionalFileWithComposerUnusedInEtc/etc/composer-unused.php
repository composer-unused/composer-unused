<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;

return static function (Configuration $config): Configuration {
    return $config
        ->setAdditionalFilesFor('test/file-dependency', [
            __DIR__ . '/../vendor/test/file-dependency/src/file.php'
        ]);
};
