<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

\PHPStan\Testing\TestCase::$useStaticReflectionProvider = true;

\PHPStan\Testing\TestCase::getContainer();
