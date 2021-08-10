<?php

namespace ClassConstantNamespace;

class Foo
{
    public const LOREM = 1;
    public const IPSUM = 2;

    public function fooMethod()
    {
        self::class;
        self::LOREM;
        self::IPSUM;
    }
}
