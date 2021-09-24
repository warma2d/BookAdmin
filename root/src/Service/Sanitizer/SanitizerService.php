<?php

namespace App\Service\Sanitizer;

class SanitizerService {

    public static function sanitizeString(string $string): string
    {
        return trim(filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    }

    public static function sanitizeIntNumber($number): int
    {
        return (int)preg_replace('%(\D*)%', '', $number);
    }
}
