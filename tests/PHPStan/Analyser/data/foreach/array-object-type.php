<?php

namespace ArrayObjectType;

use AnotherNamespace\Foo;

class Test
{
    public const ARRAY_CONSTANT = [0, 1, 2, 3];
    public const MIXED_CONSTANT = [0, 'foo'];

    public function doFoo()
    {
        /** @var Foo[] $foos */
        $foos = foos();

        foreach ($foos as $foo) {
            die;
        }
    }
}
