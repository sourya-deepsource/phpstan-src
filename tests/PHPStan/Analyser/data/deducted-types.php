<?php

namespace TypesNamespaceDeductedTypes;

use TypesNamespaceFunctions;

class Foo
{
    public const INTEGER_CONSTANT = 1;
    public const FLOAT_CONSTANT = 1.0;
    public const STRING_CONSTANT = 'foo';
    public const ARRAY_CONSTANT = [];
    public const BOOLEAN_CONSTANT = true;
    public const NULL_CONSTANT = null;

    public function doFoo()
    {
        $integerLiteral = 1;
        $booleanLiteral = true;
        $anotherBooleanLiteral = false;
        $stringLiteral = 'foo';
        $floatLiteral = 1.0;
        $floatAssignedByRef = &$floatLiteral;
        $nullLiteral = null;
        $loremObjectLiteral = new Lorem();
        $mixedObjectLiteral = new $class();
        $newStatic = new static();
        $arrayLiteral = [];
        $stringFromFunction = TypesNamespaceFunctions\stringFunction();
        $fooObjectFromFunction = TypesNamespaceFunctions\objectFunction();
        $mixedFromFunction = TypesNamespaceFunctions\unknownTypeFunction();
        $foo = new self();
        die;
    }
}
