<?php

namespace MethodSignatureGenericStaticType;

/**
 * @template T
 */
class Foo
{

	/**
	 * @return static<T>
	 */
	public function doFoo()
	{

	}

}

/**
 * @template T
 * @extends Foo<T>
 */
class Bar extends Foo
{

	/**
	 * @return static<int>
	 */
	public function doFoo()
	{

	}

}

/**
 * @template T
 * @extends Foo<T>
 */
final class FinalBar extends Foo
{

	/**
	 * @return static<int>
	 */
	public function doFoo()
	{

	}

}
