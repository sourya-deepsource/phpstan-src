<?php

namespace InferArrayKey;

use function PHPStan\Testing\assertType;

/**
 * @implements \IteratorAggregate<int, \stdClass>
 */
class Foo implements \IteratorAggregate
{
    /** @var \stdClass[] */
    private $items;

    public function getIterator()
    {
        $it = new \ArrayIterator($this->items);
        assertType('(int|string)', $it->key());

        return $it;
    }
}
