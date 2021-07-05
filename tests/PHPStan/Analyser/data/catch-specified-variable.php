<?php

namespace TryCatchWithSpecifiedVariable;

class FooException extends \Exception
{
}

function () {
    /** @var string|null $foo */
    $foo = doFoo();
    if ($foo !== null) {
        return;
    }

    try {
        maybeThrows();
    } catch (FooException $foo) {
        die;
    }
};
