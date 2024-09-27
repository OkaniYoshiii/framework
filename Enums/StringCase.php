<?php

namespace Framework\Enums;

enum StringCase : string
{
    case TITLE_CASE = 'TitleCase';
    case CAMEL_CASE = 'camelCase';
    case SNAKE_CASE = 'snake_case';

    public static function values() 
    {
        return array_map(fn(StringCase $case) => $case->value, self::cases());
    }
}