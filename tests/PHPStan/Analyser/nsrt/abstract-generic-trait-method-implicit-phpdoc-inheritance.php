<?php

namespace AbstractGenericTraitMethodImplicitPhpDocInheritance;

use function PHPStan\Testing\assertType;

/**
 * @template T
 */
trait Foo
{

	/** @return T */
	abstract public function doFoo();
}

class UseFoo
{

	/** @use Foo<int> */
	use Foo;

	public function doFoo()
	{
		return 1;
	}

}

function (UseFoo $f): void {
	assertType('int', $f->doFoo());
};
