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

    public function typeDeclaration() : string
    {
        return match($this) {
            self::STRING => 'string',
            self::INTEGER => 'int',
            self::FLOAT => 'float',
            self::BOOLEAN => 'bool',
            self::ARRAY => 'array',
            self::OBJECT => 'object'
        };
    }
}