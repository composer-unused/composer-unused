<?php

declare(strict_types=1);

namespace A {
    interface InterfaceA {}
}

namespace B {
    interface InterfaceB {}
}

namespace ClassImplements {
    class TestImpl implements \A\InterfaceA, \B\InterfaceB {}
}
