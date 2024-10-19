<?php

namespace OkaniYoshiii\Framework\Enums;

use OkaniYoshiii\Framework\Contracts\Traits\EnumTrait;

enum DataType : string
{
    use EnumTrait;
    
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'double';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case OBJECT = 'object';
}