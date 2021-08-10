<?php

namespace StaticMethodCallStatementNoSideEffects;

class BzzStatic
{
    public static function regular(string $a): string
    {
        return $a;
    }

    /**
     * @phpstan-pure
     */
    public static function pure1(string $a): string
    {
        return $a;
    }

    /**
     * @psalm-pure
     */
    public static function pure2(string $a): string
    {
        return $a;
    }

    /**
     * @pure
     */
    public static function pure3(string $a): string
    {
        return $a;
    }
}

function (): void {
    BzzStatic::regular('test');
    BzzStatic::pure1('test');
    BzzStatic::pure2('test');
    BzzStatic::pure3('test');
};

class PureThrows
{
    /**
     * @phpstan-pure
     * @throws void
     */
    public static function pureAndThrowsVoid()
    {
    }

    /**
     * @phpstan-pure
     * @throws \Exception
     */
    public static function pureAndThrowsException()
    {
    }

    public function doFoo(): void
    {
        self::pureAndThrowsVoid();
        self::pureAndThrowsException();
    }
}
