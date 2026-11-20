<?php

declare(strict_types=1);

function testfunction(): string {
    return '';
}

if (!function_exists('testfunction2')) {
    function testfunction2() {}
}
