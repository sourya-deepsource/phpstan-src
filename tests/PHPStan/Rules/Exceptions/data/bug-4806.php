<?php

namespace Bug4806;

class NeverThrows
{
    /**
     * @throws void
     */
    final public function __construct()
    {
    }
}

class HasNoConstructor
{
}

class MayThrowArgumentCountError
{
    /**
     * @throws \ArgumentCountError
     */
    public function __construct()
    {
        throw new \ArgumentCountError();
    }
}

class ImplicitThrow
{
    public function __construct()
    {
    }
}

class Foo
{
    /**
     * @param class-string $class
     */
    public function createNotSpecified(string $class): object
    {
        try {
            $object = new $class();
        } catch (\ArgumentCountError $error) {
        }

        return $object;
    }

    /**
     * @param class-string<NeverThrows> $class
     */
    public function createNeverThrows(string $class): object
    {
        try {
            $object = new $class();
        } catch (\ArgumentCountError $throwable) {
        }

        return $object;
    }

    /**
     * @param class-string<MayThrowArgumentCountError> $class
     */
    public function createMayThrowArgumentCountError(string $class): object
    {
        try {
            $object = new $class();
        } catch (\ArgumentCountError $error) {
        }

        return $object;
    }

    /**
     * @param class-string<MayThrowArgumentCountError> $class
     */
    public function createMayThrowArgumentCountErrorB(string $class): object
    {
        try {
            $object = new $class();
        } catch (\Throwable $throwable) {
        }

        return $object;
    }

    /**
     * @param class-string<ImplicitThrow> $class
     */
    public function implicitThrow(string $class): void
    {
        try {
            $object = new $class();
        } catch (\Throwable $throwable) {
        }
    }

    /**
     * @param class-string<HasNoConstructor> $class
     */
    public function hasNoConstructor(string $class): void
    {
        try {
            $object = new $class();
        } catch (\Throwable $throwable) {
        }
    }
}
