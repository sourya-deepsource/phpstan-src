<?php declare(strict_types = 1);

use function PHPStan\Testing\assertType;

function (): void {
	$content = file_get_contents('');
	if ($content == '') {
		die;
	}

	assertType('string', $content);
};
