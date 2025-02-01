<?php // lint >= 8.1

namespace AttributeReflectionTest;

enum FooEnum
{

	#[MyAttr(one: 15, two: 16)]
	case TEST;

}
