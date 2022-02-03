<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

final class FormatterFactory
{
    public static function create(string $type): OutputFormatterInterface
    {
        switch ($type) {
            case 'github':
                return new GithubFormatter();
            default:
                return new DefaultFormatter();
        }
    }
}
