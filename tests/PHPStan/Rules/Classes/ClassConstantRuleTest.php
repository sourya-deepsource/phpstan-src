<?php declare(strict_types = 1);

namespace PHPStan\Rules\Classes;

use PHPStan\Php\PhpVersion;
use PHPStan\Rules\ClassCaseSensitivityCheck;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;

/**
 * @extends \PHPStan\Testing\RuleTestCase<ClassConstantRule>
 */
class ClassConstantRuleTest extends \PHPStan\Testing\RuleTestCase
{

	/** @var int */
	private $phpVersion;

	protected function getRule(): Rule
	{
		$broker = $this->createReflectionProvider();
		return new ClassConstantRule($broker, new RuleLevelHelper($broker, true, false, true, false), new ClassCaseSensitivityCheck($broker, true), new PhpVersion($this->phpVersion));
	}

	public function testClassConstant(): void
	{
		$this->phpVersion = PHP_VERSION_ID;
		$this->analyse(
			[
				__DIR__ . '/data/class-constant.php',
				__DIR__ . '/data/class-constant-defined.php',
			],
			[
				[
					'Class ClassConstantNamespace\Bar not found.',
					6,
					'Learn more at https://phpstan.org/user-guide/discovering-symbols',
				],
				[
					'Using self outside of class scope.',
					7,
				],
				[
					'Access to undefined constant ClassConstantNamespace\Foo::DOLOR.',
					10,
				],
				[
					'Access to undefined constant ClassConstantNamespace\Foo::DOLOR.',
					16,
				],
				[
					'Using static outside of class scope.',
					18,
				],
				[
					'Using parent outside of class scope.',
					19,
				],
				[
					'Access to constant FOO on an unknown class ClassConstantNamespace\UnknownClass.',
					21,
					'Learn more at https://phpstan.org/user-guide/discovering-symbols',
				],
				[
					'Class ClassConstantNamespace\Foo referenced with incorrect case: ClassConstantNamespace\FOO.',
					26,
				],
				[
					'Class ClassConstantNamespace\Foo referenced with incorrect case: ClassConstantNamespace\FOO.',
					27,
				],
				[
					'Access to undefined constant ClassConstantNamespace\Foo::DOLOR.',
					27,
				],
				[
					'Class ClassConstantNamespace\Foo referenced with incorrect case: ClassConstantNamespace\FOO.',
					28,
				],
				[
					'Access to undefined constant ClassConstantNamespace\Foo|string::DOLOR.',
					33,
				],
			]
		);
	}

	public function testClassConstantVisibility(): void
	{
		if (!self::$useStaticReflectionProvider && PHP_VERSION_ID >= 70400) {
			$this->markTestSkipped('Test does not run on PHP 7.4 because of referencing parent:: without parent class.');
		}

		$this->phpVersion = PHP_VERSION_ID;
		$this->analyse([__DIR__ . '/data/class-constant-visibility.php'], [
			[
				'Access to private constant PRIVATE_BAR of class ClassConstantVisibility\Bar.',
				25,
			],
			[
				'Access to parent::BAZ but ClassConstantVisibility\Foo does not extend any class.',
				27,
			],
			[
				'Access to undefined constant ClassConstantVisibility\Bar::PRIVATE_FOO.',
				45,
			],
			[
				'Access to private constant PRIVATE_FOO of class ClassConstantVisibility\Foo.',
				46,
			],
			[
				'Access to private constant PRIVATE_FOO of class ClassConstantVisibility\Foo.',
				47,
			],
			[
				'Access to undefined constant ClassConstantVisibility\Bar::PRIVATE_FOO.',
				60,
			],
			[
				'Access to protected constant PROTECTED_FOO of class ClassConstantVisibility\Foo.',
				71,
			],
			[
				'Access to undefined constant ClassConstantVisibility\WithFooAndBarConstant&ClassConstantVisibility\WithFooConstant::BAZ.',
				106,
			],
			[
				'Access to undefined constant ClassConstantVisibility\WithFooAndBarConstant|ClassConstantVisibility\WithFooConstant::BAR.',
				110,
			],
			[
				'Access to constant FOO on an unknown class ClassConstantVisibility\UnknownClassFirst.',
				112,
				'Learn more at https://phpstan.org/user-guide/discovering-symbols',
			],
			[
				'Access to constant FOO on an unknown class ClassConstantVisibility\UnknownClassSecond.',
				112,
				'Learn more at https://phpstan.org/user-guide/discovering-symbols',
			],
			[
				'Cannot access constant FOO on int|string.',
				116,
			],
			[
				'Access to undefined constant static(ClassConstantVisibility\AccessWithStatic)::BAR.',
				129,
			],
			[
				'Class ClassConstantVisibility\Foo referenced with incorrect case: ClassConstantVisibility\FOO.',
				135,
			],
			[
				'Access to private constant PRIVATE_FOO of class ClassConstantVisibility\Foo.',
				135,
			],
		]);
	}

	public function testClassExists(): void
	{
		$this->phpVersion = PHP_VERSION_ID;
		$this->analyse([__DIR__ . '/data/class-exists.php'], [
			[
				'Class UnknownClass\Bar not found.',
				24,
				'Learn more at https://phpstan.org/user-guide/discovering-symbols',
			],
			[
				'Class UnknownClass\Foo not found.',
				26,
				'Learn more at https://phpstan.org/user-guide/discovering-symbols',
			],
			[
				'Class UnknownClass\Foo not found.',
				29,
				'Learn more at https://phpstan.org/user-guide/discovering-symbols',
			],
		]);
	}

	public function dataClassConstantOnExpression(): array
	{
		return [
			[
				70400,
				[
					[
						'Accessing ::class constant on an expression is supported only on PHP 8.0 and later.',
						15,
					],
					[
						'Accessing ::class constant on an expression is supported only on PHP 8.0 and later.',
						16,
					],
					[
						'Accessing ::class constant on an expression is supported only on PHP 8.0 and later.',
						17,
					],
					[
						'Accessing ::class constant on an expression is supported only on PHP 8.0 and later.',
						18,
					],
				],
			],
			[
				80000,
				[
					[
						'Accessing ::class constant on a dynamic string is not supported in PHP.',
						16,
					],
					[
						'Cannot access constant class on stdClass|null.',
						17,
					],
					[
						'Cannot access constant class on string|null.',
						18,
					],
				],
			],
		];
	}

	/**
	 * @dataProvider dataClassConstantOnExpression
	 * @param int $phpVersion
	 * @param mixed[] $errors
	 */
	public function testClassConstantOnExpression(int $phpVersion, array $errors): void
	{
		if (!self::$useStaticReflectionProvider) {
			$this->markTestSkipped('Test requires static reflection');
		}
		$this->phpVersion = $phpVersion;
		$this->analyse([__DIR__ . '/data/class-constant-on-expr.php'], $errors);
	}

	public function testAttributes(): void
	{
		if (!self::$useStaticReflectionProvider && PHP_VERSION_ID < 80000) {
			$this->markTestSkipped('Test requires PHP 8.0.');
		}

		$this->phpVersion = PHP_VERSION_ID;
		$this->analyse([__DIR__ . '/data/class-constant-attribute.php'], [
			[
				'Access to undefined constant ClassConstantAttribute\Foo::BAR.',
				5,
			],
			[
				'Access to undefined constant ClassConstantAttribute\Foo::BAR.',
				9,
			],
			[
				'Access to undefined constant ClassConstantAttribute\Foo::BAR.',
				12,
			],
			[
				'Access to undefined constant ClassConstantAttribute\Foo::BAR.',
				15,
			],
			[
				'Access to undefined constant ClassConstantAttribute\Foo::BAR.',
				17,
			],
			[
				'Access to private constant FOO of class ClassConstantAttribute\Foo.',
				26,
			],
			[
				'Access to undefined constant ClassConstantAttribute\Foo::BAR.',
				26,
			],
		]);
	}

}
