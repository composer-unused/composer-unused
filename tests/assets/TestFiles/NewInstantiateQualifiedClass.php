<?php

declare(strict_types=1);

namespace TestFile {

    class NewInstantiateQualifiedClass
    {
    }
}

namespace {

    $var = new TestFile\NewInstantiateQualifiedClass();
}
