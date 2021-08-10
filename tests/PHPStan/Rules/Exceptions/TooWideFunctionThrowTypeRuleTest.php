<?php

declare(strict_types=1);

namespace PHPStan\Rules\Exceptions;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<TooWideFunctionThrowTypeRule>
 */
class TooWideFunctionThrowTypeRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new TooWideFunctionThrowTypeRule(new TooWideThrowTypeCheck());
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/too-wide-throws-function.php'], [
            [
                'Function TooWideThrowsFunction\doFoo4() has DomainException in PHPDoc @throws tag but it\'s not thrown.',
                26,
            ],
            [
                'Function TooWideThrowsFunction\doFoo7() has DomainException in PHPDoc @throws tag but it\'s not thrown.',
                48,
            ],
            [
                'Function TooWideThrowsFunction\doFoo8() has DomainException in PHPDoc @throws tag but it\'s not thrown.',
                57,
            ],
            [
                'Function TooWideThrowsFunction\doFoo9() has DomainException in PHPDoc @throws tag but it\'s not thrown.',
                63,
            ],
        ]);
    }
}
