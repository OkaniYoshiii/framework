<?php

namespace Framework\Enums;

use Framework\Contracts\Traits\EnumTrait;

enum DataType : string
{
    use EnumTrait;
    
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case OBJECT = 'object';
}