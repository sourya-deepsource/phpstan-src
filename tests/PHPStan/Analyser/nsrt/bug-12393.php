<?php

namespace Bug12393;

use function PHPStan\Testing\assertType;

class HelloWorld
{
	private string $name;

	/** @var string */
	private $untypedName;

	private float $float;

	/** @var float */
	private $untypedFloat;

	private array $a;

	/**
	 * @param mixed[] $plugin
	 */
	public function __construct(array $plugin){
		$this->name = $plugin["name"];
		assertType('string', $this->name);
	}

	/**
	 * @param mixed[] $plugin
	 */
	public function doFoo(array $plugin){
		$this->untypedName = $plugin["name"];
		assertType('mixed', $this->untypedName);
	}

	public function doBar(int $i){
		$this->float = $i;
		assertType('float', $this->float);
	}

	public function doBaz(int $i){
		$this->untypedFloat = $i;
		assertType('int', $this->untypedFloat);
	}

	public function doLorem(): void
	{
		$this->a = ['a' => 1];
		assertType('array{a: 1}', $this->a);
	}
}

class HelloWorldStatic
{
	private static string $name;

	/** @var string */
	private static $untypedName;

	private static float $float;

	/** @var float */
	private static $untypedFloat;

	private static array $a;

	/**
	 * @param mixed[] $plugin
	 */
	public function __construct(array $plugin){
		self::$name = $plugin["name"];
		assertType('string', self::$name);
	}

	/**
	 * @param mixed[] $plugin
	 */
	public function doFoo(array $plugin){
		self::$untypedName = $plugin["name"];
		assertType('mixed', self::$untypedName);
	}

	public function doBar(int $i){
		self::$float = $i;
		assertType('float', self::$float);
	}

	public function doBaz(int $i){
		self::$untypedFloat = $i;
		assertType('int', self::$untypedFloat);
	}

	public function doLorem(): void
	{
		self::$a = ['a' => 1];
		assertType('array{a: 1}', self::$a);
	}
}
