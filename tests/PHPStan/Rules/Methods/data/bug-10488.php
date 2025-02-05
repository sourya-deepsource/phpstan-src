<?php // lint >= 8.0

namespace Bug10488;

trait Bar
{
	/**
	 * @param array<string,mixed> $data
	 */

	abstract protected function test(array $data): void;
}

abstract class Foo
{
	use Bar;
}
