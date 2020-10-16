<?php

namespace Foo\Bar {
    class Baz {}
    class Qux {}
    class Quz {}
    class Corge {}
}

namespace {

//    // https://github.com/composer-unused/composer-unused/pull/100#discussion_r506167242
//
//    use Foo\Bar;
//
//    if ($foo instanceof Bar\Baz) {
//        // usage should be detected because class is a name, relative to imported namespace
//    }

    if ($foo instanceof Foo\Bar\Qux) {
        // usage should be detected because class is a fully-qualified name used in the root namespace
    }

    if ($foo instanceof \Foo\Bar\Quz) {
        // usage should be detected because class is a fully-qualified name relative to the root namespace
    }

    $name = 'Foo\Bar\Corge';

    if ($foo instanceof $name) {
        // usage should not be detected because class is a variable expression
    }
}
