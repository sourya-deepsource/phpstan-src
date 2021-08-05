<?php

declare(strict_types=1);

namespace PHPStan\Internal;


class BytesHelper
{
    public static function bytes(int $bytes): string
    {
        $bytes = round($bytes);
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
        foreach ($units as $unit) {
            if (abs($bytes) < 1024 || $unit === end($units)) {
                break;
            }
            $bytes /= 1024;
        }

		// skipcq: PHP-W1040
        if (!isset($unit)) {
            throw new \PHPStan\ShouldNotHappenException();
        }

        return round($bytes, 2) . ' ' . $unit;
    }
}
