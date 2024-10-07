<?php

namespace OkaniYoshiii\Framework\Enums;

use OkaniYoshiii\Framework\Contracts\Traits\EnumTrait;

enum SQLFieldType : string
{
    use EnumTrait;
    
    case STRING = 'string';
    case INTEGER = 'integer';
    case DATETIME = 'datetime';
    case TEXT = 'text';
    case PASSWORD = 'password';
    case BOOLEAN = 'boolean';

    public function mapping() : string
    {
        return match($this) {
            self::STRING => 'VARCHAR',
            self::INTEGER => 'INT',
            self::DATETIME => 'DATETIME',
            self::TEXT => 'TEXT',
            self::PASSWORD => 'VARCHAR',
            self::BOOLEAN => 'TINYINT',
        };
    }

    public function length() : ?int
    {
        return match($this) {
            self::STRING => null,
            self::INTEGER => null,
            self::DATETIME => null,
            self::TEXT => null,
            self::PASSWORD => 255,
            self::BOOLEAN => 1,
        };
    }

    public function maxLength() : ?int
    {
        return match($this) {
            self::STRING => 255,
            self::INTEGER => 11,
            self::DATETIME => null,
            self::TEXT => 5000,
            self::PASSWORD => 255,
            self::BOOLEAN => 1,
        };
    }
}