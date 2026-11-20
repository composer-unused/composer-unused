<?php

declare(strict_types=1);

namespace ExtUriRequirement;

use Uri\InvalidUriException;
use Uri\Rfc3986\Uri;
use Uri\WhatWg\InvalidUrlException;
use Uri\WhatWg\Url;

class TestClass
{
    public function testMethod(): void
    {
        $uri = new Uri('https://example.com');
        $url = new Url('https://example.com');
    }
}
