<?php

namespace Bug4076;

class Foo
{
    public function test(int $x, int $y): int
    {
        switch ($x) {
            case 0:
                return 0;
            case 1:
                if ($y == 2) {
                    // continue after the switch
                    break;
                }
                // no break
            default:
                return 99;
        }

        return -1;
    }
}
