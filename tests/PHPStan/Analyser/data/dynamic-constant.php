<?php

namespace DynamicConstants;

define('GLOBAL_PURE_CONSTANT', 123);
define('GLOBAL_DYNAMIC_CONSTANT', false);

class DynamicConstantClass
{
    public const DYNAMIC_CONSTANT_IN_CLASS = 'abcdef';
    public const PURE_CONSTANT_IN_CLASS = 'abc123def';
}

class NoDynamicConstantClass
{
    // constant name is same as in DynamicConstantClass, just to test
    public const DYNAMIC_CONSTANT_IN_CLASS = 'xyz';

    private function rip()
    {
        die;
    }
}
