<?php

namespace InheritAbstractTraitMethodPhpDoc;

use function PHPStan\Testing\assertType;

trait FooTrait
{

	/** @return int */
	abstract public function doFoo();

	/** @return int */
	public function doBar()
	{
		return 1;
	}

}

class Foo
{

	use FooTrait;

	public function doFoo()
	{
		return 1;
	}

	public function doBar()
	{
		return 1;
	}

}

function (Foo $foo): void {
	assertType('int', $foo->doFoo());
	assertType('mixed', $foo->doBar());
};
