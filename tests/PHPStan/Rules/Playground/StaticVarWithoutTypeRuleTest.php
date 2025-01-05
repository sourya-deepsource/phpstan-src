<?php declare(strict_types = 1);

namespace PHPStan\Rules\Playground;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;

/**
 * @extends RuleTestCase<StaticVarWithoutTypeRule>
 */
class StaticVarWithoutTypeRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new StaticVarWithoutTypeRule(self::getContainer()->getByType(FileTypeMapper::class));
	}

	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/static-var-without-type.php'], [
			[
				'Static variable needs to be typed with PHPDoc @var tag.',
				23,
			],
			[
				'Static variable needs to be typed with PHPDoc @var tag.',
				28,
			],
		]);
	}

}
