<?php

namespace OkaniYoshiii\Framework\Helpers;

use Exception;

class StringHelper
{
    public static function camelCaseToSnakeCase(string $string) : string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public static function snakeCaseToCamelCase(string $string) : string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    public static function toTitleCase(string $string) : string
    {
        return str_replace('_', '', mb_convert_case($string, MB_CASE_TITLE));
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