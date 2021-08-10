<?php

namespace Bug4729;

/** @template T of int */
interface I
{
    /**
     * @return static
     */
    public function get(): I;
}

/**
 * @template T of int
 * @implements I<T>
 */
final class B implements I
{
    public function get(): I
    {
        return $this;
    }
}

/**
 * @template T of int
 * @implements I<T>
 */
class C implements I
{
    public function get(): I
    {
        return $this;
    }
}
