<?php

namespace TraitUseError;

class Foo
{
    use FooTrait;
}

trait BarTrait
{
    use Foo;
    use FooTrait;
}

interface Baz
{
    use BarTrait;

    }

new class() {
    use FooTrait;
    use Baz;
};
