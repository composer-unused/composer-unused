<?php

declare(strict_types=1);

namespace ComposerJsonNotInRoot;

use FileDependency\DependencyClass;

class TestClass
{
    public function testMethod(): void
    {
        $a = new DependencyClass();
    }
}
