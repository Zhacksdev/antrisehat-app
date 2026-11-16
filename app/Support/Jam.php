<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Helper kecil agar format jam konsisten (H:i:s) di MySQL maupun SQLite.
 */
final class Jam
{
    public static function normalisasi(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse((string) $value)->format('H:i:s');
    }

    public static function pendek(?string $value): string
    {
        return $value ? substr($value, 0, 5) : '-';
    }
}
