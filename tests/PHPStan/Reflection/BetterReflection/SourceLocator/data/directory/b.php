<?php

class BFoo
{
    public function doBar()
    {
    }
}

function doBar()
{
}

function doBaz()
{
}

function &get_smarty()
{
    global $smarty;

    return $smarty;
}

function & get_smarty2()
{
    global $smarty;

    return $smarty;
}
