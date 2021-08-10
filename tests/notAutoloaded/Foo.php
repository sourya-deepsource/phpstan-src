<?php

declare(strict_types=1);

namespace SomeOtherNamespace\Tests;

class Foo
{
    public const FOO_CONST = 'foo';

    /** @var string */
    private $fooProperty;

    public function doFoo(): string
    {
        $this->fooProperty = 'test';

        return $this->fooProperty;
    }
}
