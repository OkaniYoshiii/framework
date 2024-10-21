<?php

namespace OkaniYoshiii\Framework\Helpers;

use Exception;
use OkaniYoshiii\Framework\Types\Primitive\CamelCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\PascalCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;

class StringHelper
{
    public static function camelCaseToSnakeCase(CamelCaseWord $string) : SnakeCaseWord
    {
        $string = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string->getValue()));

        return new SnakeCaseWord($string);
    }

    public static function snakeCaseToCamelCase(SnakeCaseWord $string) : CamelCaseWord
    {
        $string = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string->getValue()))));

        return new CamelCaseWord($string);
    }

    public static function pascalCaseToSnakeCase(PascalCaseWord $string) : SnakeCaseWord
    {
        $string = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string->getValue()));

        return new SnakeCaseWord($string);
    }

    public static function pascalCaseToCamelCase(PascalCaseWord $string) : CamelCaseWord
    {
        $string = lcfirst($string->getValue());

        return new CamelCaseWord($string);
    }

    public static function camelCaseToPascalCase(CamelCaseWord $string) : PascalCaseWord
    {
        $string = ucfirst($string->getValue());

        return new PascalCaseWord($string);
    }

    public static function stringToTitleCase(string $string) : string
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

    public static function isPascalCase(string $string) : bool
    {
        $regexp = '/^[A-Z][A-Za-z]+$/';
        return match(preg_match($regexp, $string)) {
            0 => false,
            1 => true,
            false => throw new Exception('An error occured in the following regexp expression : ' . $regexp),
        };
    }

    public static function isWord(string $string) : bool
    {
        $regexp = '/^[A-Z_a-z]+$/';
        return match(preg_match($regexp, $string)) {
            0 => false,
            1 => true,
            false => throw new Exception('An error occured in the following regexp expression : ' . $regexp),
        };
    }
}