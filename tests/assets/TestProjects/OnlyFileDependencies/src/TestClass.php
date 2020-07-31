<?php

declare(strict_types=1);

namespace EmptyRequire;

use function testfunction;

class TestClass
{
    public function testMethod(): void
    {
        $a = testfunction();
        $b = testfunction();
    }
}
