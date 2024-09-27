<?php

namespace Framework\Enums;

use Framework\Contracts\Traits\EnumTrait;

enum StringCase : string
{
    use EnumTrait;
    
    case TITLE_CASE = 'TitleCase';
    case CAMEL_CASE = 'camelCase';
    case SNAKE_CASE = 'snake_case';
}