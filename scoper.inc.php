<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

$stubs = [];

$stubFinder = Finder::create();

foreach ($stubFinder->files()->name('*.php')->in([
    __DIR__ . '/vendor/symfony/polyfill-php81',
    __DIR__ . '/vendor/symfony/polyfill-php80',
    __DIR__ . '/vendor/symfony/polyfill-mbstring',
    __DIR__ . '/vendor/symfony/polyfill-intl-normalizer',
    __DIR__ . '/vendor/symfony/polyfill-intl-grapheme',
]) as $file) {
    $stubs[] = $file->getPathName();
}

return [
    'prefix' => '__ComposerUnused__',
    'exclude-files' => $stubs,

    // By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
    // directory. You can however define which files should be scoped by defining a collection of Finders in the
    // following configuration key.
    //
    // For more see: https://github.com/humbug/php-scoper#finders-and-paths
    'finders' => [
        Finder::create()->files()->in('src'),
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
            ->exclude([
                'doc',
                'test',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
            ])
            ->in('vendor'),
        Finder::create()->append([
            'composer.json',
            'config/container.php',
            'config/services.php'
        ]),
    ],

    // PHP-Scoper's goal is to make sure that all code for a project lies in a distinct PHP namespace. However, you
    // may want to share a common API between the bundled code of your PHAR and the consumer code. For example if
    // you have a PHPUnit PHAR with isolated code, you still want the PHAR to be able to understand the
    // PHPUnit\Framework\TestCase class.
    //
    // A way to achieve this is by specifying a list of classes to not prefix with the following configuration key. Note
    // that this does not work with functions or constants neither with classes belonging to the global namespace.
    //
    // Fore more see https://github.com/humbug/php-scoper#whitelist
    'whitelist' => [
        \Webmozart\Glob\Glob::class,
        'Symfony\Polyfill\Php80\*',
        'Symfony\Polyfill\Mbstring\*',
        'Symfony\Polyfill\Intl\Normalizer\*',
        'Symfony\Polyfill\Intl\Grapheme\*',
    ],
    'exclude-namespaces' => [
        #'ComposerUnused\ComposerUnused\Configuration'
    ],
    'expose-namespaces' => [
        'Symfony\Polyfill',
        'ComposerUnused\ComposerUnused\Configuration'
    ]
];
