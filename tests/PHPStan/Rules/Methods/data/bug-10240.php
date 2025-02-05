<?php // lint >= 8.0

namespace Bug10240;

interface MyInterface
{
	/**
	 * @phpstan-param truthy-string $truthyStrParam
	 */
	public function doStuff(
		string $truthyStrParam,
	): void;
}

trait MyTrait
{
	/**
	 * @phpstan-param truthy-string $truthyStrParam
	 */
	abstract public function doStuff(
		string $truthyStrParam,
	): void;
}

class MyClass implements MyInterface
{
	use MyTrait;

	public function doStuff(
		string $truthyStrParam,
	): void
	{
		// ...
	}
}
