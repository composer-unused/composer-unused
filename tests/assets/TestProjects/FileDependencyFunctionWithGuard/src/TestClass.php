<?php

declare(strict_types=1);

namespace FileDependencyFunctionWithGuard;

class TestClass
{
    public function testMethod(): void
    {
        $b = testfunction2();
    }
}
