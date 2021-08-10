<?php

namespace Bug3266;

use function PHPStan\Testing\assertType;

class Foo
{
    /**
     * @phpstan-template TKey
     * @phpstan-template TValue
     * @phpstan-param  array<TKey, TValue>  $iterator
     * @phpstan-return array<TKey, TValue>
     */
    public function iteratorToArray($iterator)
    {
        assertType('array<TKey (method Bug3266\Foo::iteratorToArray(), argument), TValue (method Bug3266\Foo::iteratorToArray(), argument)>', $iterator);
        $array = [];
        foreach ($iterator as $key => $value) {
            assertType('TKey (method Bug3266\Foo::iteratorToArray(), argument)', $key);
            assertType('TValue (method Bug3266\Foo::iteratorToArray(), argument)', $value);
            $array[$key] = $value;
            assertType('array<TKey (method Bug3266\Foo::iteratorToArray(), argument), TValue (method Bug3266\Foo::iteratorToArray(), argument)>&nonEmpty', $array);
        }

        assertType('array<TKey (method Bug3266\Foo::iteratorToArray(), argument), TValue (method Bug3266\Foo::iteratorToArray(), argument)>', $array);

        return $array;
    }
}
