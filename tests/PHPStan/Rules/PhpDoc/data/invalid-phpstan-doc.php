<?php

namespace InvalidPHPStanDoc;

class Baz
{
}
/** @phpstan-extens Baz */
class Boo extends Baz
{
    /**
     * @phpstan-template T
     * @phpstan-pararm class-string<T> $a
     * @phpstan-return T
     */
    public function foo(string $a)
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @phpstan-pararm
     */
    public function bar()
    {
    }

    public function baz()
    {
        /** @phpstan-va */$a = $b; /** @phpstan-ignore-line */
        $c = 'foo';
    }

    /**
     * @phpstan-throws void
     */
    public function any()
    {
    }
}
