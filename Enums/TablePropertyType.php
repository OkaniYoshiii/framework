<?php

namespace Framework\Enums;

use Framework\Contracts\Traits\EnumTrait;

enum TablePropertyType : string
{
    use EnumTrait;
    
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case DATETIME = 'datetime';
    case TEXT = 'text';
    case PASSWORD = 'password';
    case BOOLEAN = 'boolean';
}