<?php

namespace OkaniYoshiii\Framework\Enums;

use OkaniYoshiii\Framework\Contracts\Traits\EnumTrait;

enum StringCase : string
{
    use EnumTrait;
    
    case TITLE_CASE = 'TitleCase';
    case CAMEL_CASE = 'camelCase';
    case SNAKE_CASE = 'snake_case';
}