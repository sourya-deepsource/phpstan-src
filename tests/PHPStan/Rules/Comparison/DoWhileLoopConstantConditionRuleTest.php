<?php declare(strict_types = 1);

namespace PHPStan\Rules\Comparison;

/**
 * @extends \PHPStan\Testing\RuleTestCase<DoWhileLoopConstantConditionRule>
 */
class DoWhileLoopConstantConditionRuleTest extends \PHPStan\Testing\RuleTestCase
{

	/** @var bool */
	private $treatPhpDocTypesAsCertain = true;

	protected function getRule(): \PHPStan\Rules\Rule
	{
		return new DoWhileLoopConstantConditionRule(
			new ConstantConditionRuleHelper(
				new ImpossibleCheckTypeHelper(
					$this->createReflectionProvider(),
					$this->getTypeSpecifier(),
					[],
					$this->treatPhpDocTypesAsCertain
				),
				$this->treatPhpDocTypesAsCertain
			),
			$this->treatPhpDocTypesAsCertain
		);
	}

	protected function shouldTreatPhpDocTypesAsCertain(): bool
	{
		return $this->treatPhpDocTypesAsCertain;
	}

	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/do-while-loop.php'], [
			[
				'Do-while loop condition is always true.',
				12,
			],
			[
				'Do-while loop condition is always false.',
				37,
			],
			[
				'Do-while loop condition is always false.',
				46,
			],
			[
				'Do-while loop condition is always false.',
				55,
			],
			[
				'Do-while loop condition is always true.',
				64,
			],
			[
				'Do-while loop condition is always false.',
				73,
			],
		]);
	}

}
