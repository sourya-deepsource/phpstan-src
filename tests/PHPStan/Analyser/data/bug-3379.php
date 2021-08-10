<?php

namespace Bug3379;

class Foo
{
    public const URL = SOME_UNKNOWN_CONST . '/test';
}

function () {
    echo Foo::URL;
};
