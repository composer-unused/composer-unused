<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => '__ComposerUnused__',

    'expose-classes' => [
        \Webmozart\Glob\Glob::class,
    ],
    'exclude-constants' => [
        // Symfony global constants
        '/^SYMFONY\_[\p{L}_]+$/',
    ],
    'expose-namespaces' => [
        'ComposerUnused\ComposerUnused\Configuration'
    ]
];
