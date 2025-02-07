<?php

namespace Bug12327;

trait SomeInternalTrait__TheNameIsIrrelevant
{
	public function something(): void {}
}

class DoesNotMatter
{
	use SomeInternalTrait__TheNameIsIrrelevant {
		SomeInternalTrait__TheNameIsIrrelevant::something as methodAlias;
	}
	use ThisTriggersTheIssue;

	public function anything(): void {}
}
