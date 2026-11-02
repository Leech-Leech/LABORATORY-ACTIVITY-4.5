<?php

namespace App\Support;

/**
 * Money is calculated in integer cents to avoid floating-point drift,
 * then written back to DECIMAL(10,2) columns as "0.00" strings.
 */
final class Money
{
    public static function toCents(string|int|float|null $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    public static function fromCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
