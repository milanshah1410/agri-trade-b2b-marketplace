<?php

namespace App\Support;

class Phone
{
    /**
     * Normalise an Indian mobile number to its 10 digits
     * ("+91 98765-43210", "098765 43210" and "9876543210" all become "9876543210").
     */
    public static function normalize(?string $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return substr($digits, 2);
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        return $digits;
    }
}
