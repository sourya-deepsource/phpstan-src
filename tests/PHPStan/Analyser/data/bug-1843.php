<?php

namespace Bug1843;

class HelloWorld
{
    public const W = '1';

    public const P = [
        self::W => [
            'A' => '2',
            'B' => '3',
            'C' => '4',
            'D' => '5',
            'E' => '6',
            'F' => '7',
        ],
    ];

    public function sayHello(): void
    {
        echo self::P[self::W]['A'];
    }
}
