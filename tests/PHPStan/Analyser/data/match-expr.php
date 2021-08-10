<?php

// lint >= 8.0

namespace MatchExpr;

use function PHPStan\Testing\assertType;

class Foo
{
    /**
     * @param 1|2|3|4 $i
     */
    public function doFoo(int $i): void
    {
        assertType('*NEVER*', match ($i) {
            0 => $i,
        });
        assertType('1|2|3|4', $i);
        assertType('1', match ($i) {
            1 => $i,
        });
        assertType('1|2|3|4', $i);
        assertType('1|2', match ($i) {
            1, 2 => $i,
        });
        assertType('1|2|3|4', $i);
        assertType('1|2|3', match ($i) {
            1, 2, 3 => $i,
        });
        assertType('1|2|3|4', $i);
        assertType('2|3', match ($i) {
            1 => exit(),
            2, 3 => $i,
        });
        assertType('1|2|3|4', $i);
    }

    /**
     * @param 1|2|3|4 $i
     */
    public function doBar(int $i): void
    {
        match ($i) {
            0 => assertType('*NEVER*', $i),
            default => assertType('1|2|3|4', $i),
        };
        assertType('1|2|3|4', $i);
        match ($i) {
            1 => assertType('1', $i),
            // no break
            default => assertType('2|3|4', $i),
        };
        assertType('1|2|3|4', $i);
        match ($i) {
            1, 2 => assertType('1|2', $i),
            // no break
            default => assertType('3|4', $i),
        };
        assertType('1|2|3|4', $i);
        match ($i) {
            1, 2, 3 => assertType('1|2|3', $i),
            // no break
            default => assertType('4', $i),
        };
        assertType('1|2|3|4', $i);

        match ($i) {
            assertType('1|2|3|4', $i), 1, assertType('2|3|4', $i) => null,
            assertType('2|3|4', $i) => null,
            // no break
            default => assertType('2|3|4', $i),
        };
    }
}
