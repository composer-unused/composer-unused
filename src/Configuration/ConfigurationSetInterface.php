<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Configuration;

interface ConfigurationSetInterface
{
    public function apply(Configuration $configuration): Configuration;

    public function getName(): string;

    public function getDescription(): string;
}
