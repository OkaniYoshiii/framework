<?php

namespace Framework\Helpers;

use Exception;

class StringHelper
{
    public static function camelCaseToSnakeCase(string $string) : string
    {
        $words = preg_split('/(?=[A-Z])/', $string, -1, PREG_SPLIT_NO_EMPTY);
        $snakeCase = implode('_', $words);
        $snakeCase = strtolower($snakeCase);
        
        return $snakeCase;
    }

    public static function isSnakeCase(string $string) : bool
    {
        $regexp = '/^[a-z_]+$/';
        return match(preg_match($regexp, $string)) {
            0 => false,
            1 => true,
            false => throw new Exception('An error occured in the following regexp expression : ' . $regexp),
        };
    }

    public static function isCamelCase(string $string) : bool
    {
        $regexp = '/^[a-z][A-Za-z]+$/';
        return match(preg_match($regexp, $string)) {
            0 => false,
            1 => true,
            false => throw new Exception('An error occured in the following regexp expression : ' . $regexp),
        };
    }

    public static function isTitleCase(string $string) : bool
    {
        $regexp = '/^[A-Z][A-Za-z]+$/';
        return match(preg_match($regexp, $string)) {
            0 => false,
            1 => true,
            false => throw new Exception('An error occured in the following regexp expression : ' . $regexp),
        };
    }
}