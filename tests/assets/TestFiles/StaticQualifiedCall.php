<?php

declare(strict_types=1);

namespace TestFile {

    class StaticQualifiedCall
    {
        public static function bar(): void
        {
        }
    }
}

namespace {

    TestFile\StaticQualifiedCall::bar();
}
