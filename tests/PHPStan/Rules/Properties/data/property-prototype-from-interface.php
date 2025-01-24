<?php // lint >= 8.4

namespace Bug12466;

interface Foo
{

	public int $a { get; set;}

}

class Bar implements Foo
{

	public string $a;

}
