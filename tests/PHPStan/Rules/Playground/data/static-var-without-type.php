<?php

namespace StaticVarWithoutType;

class Foo
{

	public function doFoo(): void
	{
		/** @var int */
		static $i = 0;
	}

	public function doBar(): void
	{
		/** @var int $i */
		static $i = 0;
	}

	public function doBaz(): void
	{
		/** @var int $j */
		static $i = 0;
	}

	public function doLorem(): void
	{
		static $i = 0;
	}

}
