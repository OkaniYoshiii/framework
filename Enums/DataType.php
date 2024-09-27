<?php

namespace Framework\Enums;

use Framework\Contracts\Traits\EnumTrait;

enum DataType : string
{
    use EnumTrait;
    
    case STRING = 'string';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case OBJECT = 'object';
}