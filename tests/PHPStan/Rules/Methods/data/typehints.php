<?php

namespace TestMethodTypehints;

class FooMethodTypehints
{
    public function foo(FooMethodTypehints $foo, $bar, array $lorem): NonexistentClass
    {
    }

    public function bar(BarMethodTypehints $bar): array
    {
    }

    public function baz(...$bar): FooMethodTypehints
    {
    }

    /**
     * @param FooMethodTypehints[] $foos
     * @param BarMethodTypehints[] $bars
     * @return BazMethodTypehints[]
     */
    public function lorem($foos, $bars)
    {
    }

    /**
     * @param FooMethodTypehints[] $foos
     * @param BarMethodTypehints[] $bars
     * @return BazMethodTypehints[]
     */
    public function ipsum(array $foos, array $bars): array
    {
    }

    /**
     * @param FooMethodTypehints[] $foos
     * @param FooMethodTypehints|BarMethodTypehints[] $bars
     * @return self|BazMethodTypehints[]
     */
    public function dolor(array $foos, array $bars): array
    {
    }

    public function parentWithoutParent(parent $parent): parent
    {
    }

    /**
     * @param parent $parent
     * @return parent
     */
    public function phpDocParentWithoutParent($parent)
    {
    }

    public function badCaseTypehints(fOOMethodTypehints $foo): fOOMethodTypehintS
    {
    }

    /**
     * @param fOOMethodTypehints|\STDClass $foo
     * @return fOOMethodTypehintS|\stdclass
     */
    public function unionTypeBadCaseTypehints($foo)
    {
    }

    /**
     * @param FOOMethodTypehints $foo
     * @return FOOMethodTypehints
     */
    public function badCaseInNativeAndPhpDoc(FooMethodTypehints $foo): FooMethodTypehints
    {
    }

    /**
     * @param FooMethodTypehints $foo
     * @return FooMethodTypehints
     */
    public function anotherBadCaseInNativeAndPhpDoc(FOOMethodTypehints $foo): FOOMethodTypehints
    {
    }

    /**
     * @param array<NonexistentClass, AnotherNonexistentClass> $array
     */
    public function unknownTypesInArrays(array $array)
    {
    }
}

class CallableTypehints
{
    /** @param callable(Bla): Ble $cb */
    public function doFoo(callable $cb): void
    {
    }
}

/**
 * @template T
 */
class TemplateTypeMissingInParameter
{
    /**
     * @template U of object
     * @param class-string $class
     */
    public function doFoo(string $class): void
    {
    }

    /**
     * @template U of object
     * @param class-string<U> $class
     */
    public function doBar(string $class): void
    {
    }
}
