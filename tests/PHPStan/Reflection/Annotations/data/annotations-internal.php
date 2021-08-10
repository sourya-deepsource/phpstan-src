<?php

namespace InternalAnnotations;

function foo()
{
}

/**
 * @internal
 */
function internalFoo()
{
}

class Foo
{
    public const FOO = 'foo';

    public $foo;

    public static $staticFoo;

    public function foo()
    {
    }

    public static function staticFoo()
    {
    }
}

/**
 * @internal
 */
class InternalFoo
{
    /**
     * @internal
     */
    public const INTERNAL_FOO = 'internal_foo';

    /**
     * @internal
     */
    public $internalFoo;

    /**
     * @internal
     */
    public static $internalStaticFoo;

    /**
     * @internal
     */
    public function internalFoo()
    {
    }

    /**
     * @internal
     */
    public static function internalStaticFoo()
    {
    }
}

interface FooInterface
{
    public const FOO = 'foo';

    public function foo();

    public static function staticFoo();
}

/**
 * @internal
 */
interface InternalFooInterface
{
    /**
     * @internal
     */
    public const INTERNAL_FOO = 'internal_foo';

    /**
     * @internal
     */
    public function internalFoo();

    /**
     * @internal
     */
    public static function internalStaticFoo();
}

trait FooTrait
{
    public $foo;

    public static $staticFoo;

    public function foo()
    {
    }

    public static function staticFoo()
    {
    }
}

/**
 * @internal
 */
trait InternalFooTrait
{
    /**
     * @internal
     */
    public $internalFoo;

    /**
     * @internal
     */
    public static $internalStaticFoo;

    /**
     * @internal
     */
    public function internalFoo()
    {
    }

    /**
     * @internal
     */
    public static function internalStaticFoo()
    {
    }
}
