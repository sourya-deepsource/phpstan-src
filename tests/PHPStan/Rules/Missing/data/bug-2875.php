<?php

namespace Bug2875MissingReturn;

class A
{
}
class B
{
}

class HelloWorld
{
    /** @param A|B|null $obj */
    public function one($obj): int
    {
        if ($obj === null) {
            return 1;
        } elseif ($obj instanceof A) {
            return 2;
        } elseif ($obj instanceof B) {
            return 3;
        }
    }
}
