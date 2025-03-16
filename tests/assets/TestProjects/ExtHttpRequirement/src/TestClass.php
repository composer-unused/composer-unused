<?php

declare(strict_types=1);

namespace ExtHttpRequirement;

class TestClass
{
    public function testMethod(): void
    {
        $client = new \http\Client();
    }
}
